"""Configuration interface WSGI app for weather.im."""

from collections import defaultdict
from http import HTTPStatus
from pathlib import Path
from urllib.parse import parse_qs

from jinja2 import Environment, FileSystemLoader, select_autoescape
from pyiem.database import get_sqlalchemy_conn, sql_helper
from sqlalchemy.engine import Connection

TEMPLATE_ENV = Environment(
    loader=FileSystemLoader(Path(__file__).resolve().parent / "templates"),
    autoescape=select_autoescape(["html", "xml"]),
)
CONFIG_TEMPLATE = TEMPLATE_ENV.get_template("config_interface.html")


def _get_remote_user(environ: dict) -> str:
    """Extract remote user from WSGI environment."""
    return environ.get("REMOTE_USER", "anonymous").lower().strip()


def _load_user_configuration(
    conn: Connection,
    remote_user: str,
) -> tuple[list[dict], dict[int, list[str]]]:
    """Load user webhooks and channel subscriptions from database."""
    res = conn.execute(
        sql_helper("""
    select h.id as hook_id, h.url, h.hook_label from iembot_webhooks h JOIN
    iembot_webhook_users u on (h.iembot_webhook_user_id = u.id)
    where u.google_id = :google_id
                   """),
        {"google_id": remote_user},
    )

    webhooks = []
    hook_ids = []
    for row in res.mappings():
        webhooks.append(
            {
                "id": row["hook_id"],
                "url": row["url"],
                "hook_label": row["hook_label"] or row["url"],
            }
        )
        hook_ids.append(row["hook_id"])

    if not hook_ids:
        return webhooks, {}

    res = conn.execute(
        sql_helper("""
    select h.id as hook_id, h.hook_label,
    c.channel_name from iembot_webhooks h,
    iembot_subscriptions s, iembot_channels c where
    s.channel_id = c.id and h.iembot_account_id = s.iembot_account_id and
    h.id = ANY(:hookids)"""),
        {"hookids": hook_ids},
    )
    webhook_channels: dict[int, list[str]] = defaultdict(list)
    for row in res.mappings():
        webhook_channels[row["hook_id"]].append(row["channel_name"])

    return webhooks, webhook_channels


def _get_post_data(environ: dict) -> dict[str, str]:
    """Parse POST form data from WSGI environment."""
    content_length = int(environ.get("CONTENT_LENGTH", "0") or "0")
    body = environ["wsgi.input"].read(content_length).decode("utf-8")
    parsed = parse_qs(body, keep_blank_values=True)
    return {key: values[0] for key, values in parsed.items() if values}


def _create_webhook(
    conn: Connection,
    remote_user: str,
    webhook_url: str,
    hook_label: str,
) -> str:
    """Create a webhook for the given user."""
    if not webhook_url or not hook_label:
        return "Webhook URL and label are required."

    # Gate 1: Ensure this user exists in iembot_webhook_users
    res = conn.execute(
        sql_helper(
            """
    SELECT id FROM iembot_webhook_users WHERE google_id = :google_id
            """
        ),
        {"google_id": remote_user},
    )
    if res.rowcount == 0:
        conn.execute(
            sql_helper(
                """
    INSERT INTO iembot_webhook_users (google_id) VALUES (:google_id)
                """
            ),
            {"google_id": remote_user},
        )

    conn.execute(
        sql_helper(
            """
    INSERT INTO iembot_webhooks
    (iembot_webhook_user_id, iembot_account_id, url, hook_label) values
    ((select id from iembot_webhook_users where google_id = :google_id),
    (select create_iembot_account('webhook')),
    :webhook_url,
    :hook_label
    )
            """
        ),
        {
            "google_id": remote_user,
            "webhook_url": webhook_url,
            "hook_label": hook_label,
        },
    )
    return "Create webhook executed."


def _delete_webhook(
    conn: Connection,
    remote_user: str,
    webhook_id: str,
) -> str:
    """Delete a webhook for the given user."""
    if not webhook_id:
        return "Webhook identifier is required."

    # Gate 1 Ensure the user own this webhook
    res = conn.execute(
        sql_helper(
            """
    SELECT h.id FROM iembot_webhooks h JOIN iembot_webhook_users u
    ON (h.iembot_webhook_user_id = u.id)
    WHERE u.google_id = :google_id AND h.id = :webhook_id
"""
        ),
        {"google_id": remote_user, "webhook_id": webhook_id},
    )
    if res.rowcount == 0:
        return "Error: Webhook not found or not owned by user."

    # Step 1: Delete any subscriptions associated with this hook
    res = conn.execute(
        sql_helper(
            """
    delete from iembot_subscriptions s USING iembot_webhooks h
    WHERE s.iembot_account_id = h.iembot_account_id
    AND h.id = :webhook_id
        """
        ),
        {"webhook_id": webhook_id},
    )
    reponse_message = f"Deleted {res.rowcount} channel subscriptions. "

    # Step 2: Delete the webhook now that the subscriptions are gone
    conn.execute(
        sql_helper(
            """
    DELETE FROM iembot_webhooks where id = :webhook_id
            """
        ),
        {"webhook_id": webhook_id},
    )
    return f"Delete webhook executed. {reponse_message}"


def _create_channel_subscription(
    conn: Connection,
    remote_user: str,
    webhook_id: str,
    channel_name: str,
) -> str:
    """Create a channel subscription for one webhook."""
    if not webhook_id or not channel_name:
        return "Webhook and channel are required."

    conn.execute(
        sql_helper(
            """
    INSERT INTO iembot_subscriptions(iembot_account_id, channel_id)
    VALUES (
    (SELECT iembot_account_id FROM iembot_webhooks h JOIN
    iembot_webhook_users u ON (h.iembot_webhook_user_id = u.id)
    WHERE h.id = :webhook_id AND u.google_id = :google_id),
    (SELECT get_or_create_iembot_channel_id(:channel_name))
    )
                   """
        ),
        {
            "google_id": remote_user,
            "webhook_id": webhook_id,
            "channel_name": channel_name,
        },
    )
    return "Create channel subscription executed."


def _delete_channel_subscription(
    conn: Connection,
    remote_user: str,
    webhook_id: str,
    channel_name: str,
) -> str:
    """Delete one channel subscription for one webhook."""
    if not webhook_id or not channel_name:
        return "Webhook and channel are required."

    conn.execute(
        sql_helper(
            """
       DELETE FROM iembot_subscriptions s
       USING iembot_webhooks h, iembot_webhook_users u, iembot_channels c
       WHERE s.iembot_account_id = h.iembot_account_id
         AND s.channel_id = c.id
         AND h.iembot_webhook_user_id = u.id
         AND u.google_id = :google_id
         AND h.id = :webhook_id
         AND c.channel_name = :channel_name
            """
        ),
        {
            "google_id": remote_user,
            "webhook_id": webhook_id,
            "channel_name": channel_name,
        },
    )
    return "Delete channel subscription executed."


def _handle_post(
    conn: Connection,
    remote_user: str,
    post_data: dict[str, str],
) -> str:
    """Handle webhook create/delete actions."""
    action = post_data.get("action", "").strip().lower()
    if action == "create_webhook":
        return _create_webhook(
            conn,
            remote_user,
            post_data.get("webhook_url", "").strip(),
            post_data.get("hook_label", "").strip(),
        )
    if action == "delete_webhook":
        return _delete_webhook(
            conn,
            remote_user,
            post_data.get("webhook_id", "").strip(),
        )
    if action == "create_channel":
        return _create_channel_subscription(
            conn,
            remote_user,
            post_data.get("webhook_id", "").strip(),
            post_data.get("channel_name", "").strip(),
        )
    if action == "delete_channel":
        return _delete_channel_subscription(
            conn,
            remote_user,
            post_data.get("webhook_id", "").strip(),
            post_data.get("channel_name", "").strip(),
        )
    return "Unknown action."


def application(environ: dict, start_response: callable):
    """WSGI application entrypoint."""
    remote_user = _get_remote_user(environ)
    flash_message = ""
    with get_sqlalchemy_conn("iembot") as conn:
        if environ.get("REQUEST_METHOD", "GET").upper() == "POST":
            try:
                post_data = _get_post_data(environ)
                flash_message = _handle_post(conn, remote_user, post_data)
                conn.commit()
            except Exception as exp:
                conn.rollback()
                flash_message = f"Unable to process request: {exp}"
        user_webhooks, webhook_channels = _load_user_configuration(
            conn,
            remote_user,
        )

    rendered = CONFIG_TEMPLATE.render(
        user=remote_user,
        flash_message=flash_message,
        user_webhooks=user_webhooks,
        webhook_channels=webhook_channels,
    )

    start_response(
        f"{HTTPStatus.OK.value} {HTTPStatus.OK.phrase}",
        [("Content-Type", "text/html; charset=utf-8")],
    )
    return [rendered.encode("utf-8")]
