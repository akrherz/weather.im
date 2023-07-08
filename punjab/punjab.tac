from twisted.python import syslog

syslog.startLogging(prefix="punjab", facility=syslog.syslog.LOG_LOCAL1)

from twisted.application import internet, service
from twisted.web import resource, server, static

from punjab.httpb import HttpbService

root = static.File("./html")

# uncomment only one of the bosh lines, use_raw does no xml
# parsing/serialization but is potentially less reliable
bosh = HttpbService(0)

# You can limit servers with a whitelist.
# The whitelist is a list of strings to match domain names.
bosh.white_list = [
    "weather.im",
]
# or a black list
# bosh.block_list = ['jabber.org', '.thetofu.com']

root.putChild("http-bind", resource.IResource(bosh))


site = server.Site(root, logPath="/dev/null")

application = service.Application("punjab")
internet.TCPServer(5280, site).setServiceParent(application)

# To run this simply to twistd -y punjab.tac
