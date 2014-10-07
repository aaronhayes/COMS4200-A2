"""
COMS4200 Assignment 2 : Group Good (Group I)
POX flow statistics monitor

ENSURE: this file is in ~/pox/ext/

USAGE EXAMPLE: ./pox.py forwarding.hub stats 
Can use other forwarding devices.

Requires 'peewee' installed to work,
a script that does this is ../scripts/install-pythonsql.sh
"""

from pox.core import core
from pox.lib.util import dpidToStr
import pox.openflow.libopenflow_01 as of

from pox.openflow.of_json import *
from pox.lib.recoco import Timer
from pox.lib.revent import Event, EventHalt

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
		
		byte_count = 0
		flow_count = 0
		packet_count = 0
		
		for e in event.stats:
			byte_count += e.byte_count
			packet_count += e.packet_count
			flow_count += 1
		
		log.info("Traffic From %s: %s bytes (%s packets) over %s flows",
			dpidToStr(event.connection.dpid), byte_count, packet_count, flow_count)
                
                '''
                UNCOMMENT the following two lines to save to the database.
                '''
                #record = Test_Table(timestamp=datetime.datetime, byte_count=322)
                #record.save()
		

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

Table details are in the Test_Table class comments.
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


class Test_Table (BaseModel):
        """
        A testing table with only 2 columns.
        The SQL for this table is:
        
        CREATE TABLE IF NOT EXISTS `test_table` (
          `timestamp` date NOT NULL,
          `byte_count` int(11) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
        
        copied directly from phpmyadmin export function.
        """
        
        timestamp = DateTimeField()
        byte_count = IntegerField()


