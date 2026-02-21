"""Test weather_im/iembot_config."""

from weather_im.iembot_config import application
from werkzeug.test import Client


def test_create_webhook():
    """Test no user."""
    c = Client(application)
    resp = c.post("/", data={"action": "create_webhook"})
    assert resp.status_code == 200
    assert b"Webhook URL and label are required" in resp.data


def test_simple():
    """Test a simple call."""
    c = Client(application)
    resp = c.get("/")
    assert resp.status_code == 200
