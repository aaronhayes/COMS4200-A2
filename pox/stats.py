"""
COMS4200 Assignment 2 : Group Good (Group I)
POX flow statistics monitor

ENSURE: this file is in ~/pox/ext/

USAGE EXAMPLE: ./pox.py forwarding.hub stats 
Can use other forwarding devices.

Requires 'peewee' installed to work,
a script that does this is ../scripts/install-pythonsql.sh
:)
"""

from pox.core import core
from pox.lib.util import dpidToStr
import pox.openflow.libopenflow_01 as of

from pox.openflow.of_json import *
from pox.lib.recoco import Timer
from pox.lib.revent import Event, EventHalt

import threading
import datetime
from peewee import *

log = core.getLogger()

def timer_function ():
	"""
	Request Flow and Port Stats
	"""
	for connection in core.openflow._connections.values():
		connection.send(of.ofp_stats_request(body=of.ofp_flow_stats_request()))
		connection.send(of.ofp_stats_request(body=of.ofp_port_stats_request()))
	
	log.debug("Sent %i flow/port stats requests", len(core.openflow._connections))

class StatisticsMonitor (object) :
	"""
	POX/Openflow Module
	"""
	def __init__ (self):
		core.openflow.addListeners(self)
	
	def _handle_ConnectionUp(self, event):
		log.debug("Switch %s has come up.", event.dpid)

	def _handle_FlowStatsReceived (self, event) :
		stats = flow_stats_to_list(event.stats)
		#print (stats)
		
		dpid = dpidToStr(event.connection.dpid)
		byte_count = 0
		flow_count = 0
		packet_count = 0
		
		for e in event.stats:
			db_thread = DBWriteThread(dpid, e.byte_count, e.packet_count, 1, 
				dl_src=e.match.dl_src,
				dl_dst=e.match.dl_dst,
				nw_src=e.match.nw_src,
				nw_dst=e.match.nw_dst,
				tp_src=str(e.match.tp_src),
				tp_dst=str(e.match.tp_dst))
			db_thread.start()

		for e in event.stats:
			byte_count += e.byte_count
			packet_count += e.packet_count
			flow_count += 1
			
		
		log.info("Traffic From %s: %s bytes (%s packets) over %s flows",
			dpid, byte_count, packet_count, flow_count)


	def _handle_PortStatsReceived (self, event) :
		stats = flow_stats_to_list(event.stats)
		log.debug("Port Stats From %s : %s", dpidToStr(event.connection.dpid), stats)


def launch ():
	"""
	Main Function to Lanuch The Module
	"""

	core.registerNew(StatisticsMonitor)

	Timer(5, timer_function, recurring=True)


"""
Database implementation.
Implemented using 'peewee' which is a mysql connector that uses ORM.
Basically, instead of using string queries ('SELECT * FROM table;'), it uses objects.

Tables are defined as classes with columns being attributes.

http://peewee.readthedocs.org/en/latest/index.html
"""

'''
The test database properties are
dbname: poxdb
user: pox
passwd: pox

Table details are in the Stats class comments.
'''
db = MySQLDatabase('poxdb', host='127.0.0.1', user='pox', passwd='pox')

class BaseModel (Model):
	"""
	A base model using the mysql database.
	All tables on that database are based off this class.
	Not completely neccessary but is convention for peewee.
	"""

	class Meta:
		database = db


class Stats (BaseModel):
	"""
	Stats table container.
	"""

	dpid = CharField()
	datetime = DateTimeField()
	dl_src = CharField()
	dl_dst = CharField()
	nw_src = CharField()
	nw_dst = CharField()
	tp_src = CharField()
	tp_dst = CharField()
	byte_count = IntegerField()
	packet_count = IntegerField()
	flow_count = IntegerField()


class DBWriteThread (threading.Thread):
	"""
	Write to the database as a threaded operation.

	Arguments not specified will be entered as NULL values into the database.

	When run, this will query the database for a record with matching dpid,dl,nw & tp dst/src
	and then update if one exists or create a new record if one doesn't. Only records
	within the last <timedelta> seconds are checked. That is, if a record matches but is
	older than the timedelta, then a new record is created.
	"""
		
	def __init__ (self, dpid, byte_count, packet_count, flow_count, **kwargs):
		threading.Thread.__init__(self)
		self.dpid = dpid
		self.byte_count = byte_count
		self.packet_count = packet_count
		self.flow_count = flow_count
		# The following default to None/NULL if kwarg is not set.
		self.dl_src = kwargs.get('dl_src')
		self.dl_dst = kwargs.get('dl_dst')
		self.nw_src = kwargs.get('nw_src')
		self.nw_dst = kwargs.get('nw_dst')
		self.tp_src = kwargs.get('tp_src')
		self.tp_dst = kwargs.get('tp_dst')

	def run (self):
		# 5 seconds
		timedelta = datetime.timedelta(0, 5)

		# Run a select query on the database to find existing records for the same flow
		# in the last <timedelta> seconds.
		related_stats = Stats.select().where(
			Stats.dpid == self.dpid and
			datetime.datetime.now() - Stats.datetime < timedelta and
			Stats.dl_src == self.dl_src and
			Stats.dl_dst == self.dl_dst and
			Stats.nw_src == self.nw_src and
			Stats.nw_dst == self.nw_dst and
			Stats.tp_src == self.tp_src and
			Stats.tp_dst == self.tp_dst).limit(1)

		# If the record exists, update the dateime/byte/packets.
		# Otherwise create a new record.
		if related_stats.count() > 0:
			record = related_stats.first()
			record.byte_count += self.byte_count
			record.packet_count += self.packet_count
			record.flow_count += 1
		else:
			record = Stats(
				datetime=datetime.datetime.now(),
				dpid=self.dpid,
				dl_src=self.dl_src,
				dl_dst=self.dl_dst,
				nw_src=self.nw_src,
				nw_dst=self.nw_dst,
				tp_src=self.tp_src,
				tp_dst=self.tp_dst,
				byte_count=self.byte_count,
				packet_count=self.packet_count,
				flow_count=self.flow_count)

		# This is the database write operation.
		try:
			record.save()
		except Exception:
			log.warning("Unable to write to the database.")
			pass
				


