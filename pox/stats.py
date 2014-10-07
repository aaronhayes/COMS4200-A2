"""
COMS4200 Assignment 2 : Group Good (Group I)
POX flow statistics monitor

ENSURE: this file is in ~/pox/ext/

USAGE EXAMPLE: ./pox.py forwarding.hub stats 
Can use other forwarding devices.
"""

from pox.core import core
from pox.lib.util import dpidToStr
import pox.openflow.libopenflow_01 as of

from pox.openflow.of_json import *
from pox.lib.recoco import Timer
from pox.lib.revent import Event, EventHalt

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
		
		byte_count = 0
		flow_count = 0
		packet_count = 0
		
		for e in event.stats:
			byte_count += e.byte_count
			packet_count += e.packet_count
			flow_count += 1 # Have flows even if empty dueto stats request
		
		log.info("Traffic From %s: %s bytes (%s packets) over %s flows",
			dpidToStr(event.connection.dpid), byte_count, packet_count, flow_count)
		

	def _handle_PortStatsReceived (self, event) :
		stats = flow_stats_to_list(event.stats)
		log.debug("Port Stats From %s : %s", dpidToStr(event.connection.dpid), stats)

def launch ():
	"""
	Main Function to Lanuch The Module
	"""

	core.registerNew(StatisticsMonitor)

	Timer(5, timer_function, recurring=True)

