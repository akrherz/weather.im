"""Test weather_im/iembot_config."""

from weather_im.iembot_config import application
from werkzeug.test import Client


def test_simple():
    """Test a simple call."""
    c = Client(application)
    resp = c.get("/")
    assert resp.status_code == 200
