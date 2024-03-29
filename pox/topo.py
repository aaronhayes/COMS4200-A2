"""
COMS4200 Assignment 2 : Group Good (Group I)
POX topology mointor


Calculate the network topology with switches and links.
Use JSON/Webserver to serve data

Depends on openflow.discovery, web.webcore, host_tracker

based on https://github.com/MurphyMc/poxdesk/blob/master/tinytopo.py
"""

import sys
from pox.core import core
import pox.openflow.libopenflow_01 as of
from pox.openflow.of_json import *
from pox.lib.util import dpidToStr,strToDPID, fields_of
from pox.web.jsonrpc import JSONRPCHandler, make_error
import threading
import json

log = core.getLogger()


class TopoRequestHandler (JSONRPCHandler):
	def _exec_get_topo (self):
		return core.topo.create_json()	


class Topo (object):
  	def __init__ (self):
    		self.switches = set()
    		self.links = set()
		self.hosts = set()
		core.openflow.addListeners(self)
		core.openflow_discovery.addListeners(self)
		core.host_tracker.addListeners(self)
    		log.debug("Ready to calculate topo.")

  	
	def _handle_ConnectionUp (self, event):
    		self.switches.add(dpidToStr(event.dpid))
		log.debug("Added Switch %s.", dpidToStr(event.dpid))


  	def _handle_ConnectionDown (self, event):
    		self.switches.remove(dpidToStr(event.dpid))
		log.debug("Removed Switch %s.", dpidToStr(event.dpid))


	def _handle_LinkEvent (self, event):
		s1 = event.link.dpid1
		s2 = event.link.dpid2
		if s1 > s2: 
			s1,s2 = s2,s1
    		s1 = dpidToStr(s1)
    		s2 = dpidToStr(s2)

    		if event.added:
      			self.links.add((s1,s2))
    		elif event.removed and (s1,s2) in self.links:
      			self.links.remove((s1,s2))
		log.debug("Discovered Link Event between %s and %s.", s1, s2)
	
	def _handle_HostEvent (self, event):
		h = str(event.entry.macaddr)
		s = dpidToStr(event.entry.dpid)
		log.debug("Discovered Host Event for %s, under switch %s.", h, s)
		
		if event.leave:
			if h in self.hosts:
				self.hosts.remove(h)
			if (h,s) in self.links:
				self.links.remove((h,s))
		else:
			if h not in self.hosts:
				self.hosts.add(h)
		#	if (h,s) not in self.links:
				self.links.add((h,s))

				

	def create_json (self):
		res = {}
		res['hosts'] = []
		res['switches'] = []
		res['links'] = []
		for h in self.hosts:
			res['hosts'].append({'dpid':h})
		for s in self.switches:
			res['switches'].append({'dpid':s})
		for l in self.links:
			#if l[0] not in res['switches'] and l[1] not in res['switches']:
			res['links'].append(l)
		#jsonstr = json.JSONEncoder().encode({"result": ds})
		#log.debug("JSON Reply: %s", jsonstr)
		return {"result": res}
	

def launch ():
	topo = Topo()
	core.register("topo", topo)	
	core.WebServer.set_handler("/Topo/", TopoRequestHandler, {}, True)
