DROP TABLE IF EXISTS `stats`;
CREATE TABLE `stats` (
`id` int(11) NOT NULL AUTO_INCREMENT,
  `dpid` varchar(64) NOT NULL,
  `datetime` datetime NOT NULL,
  `dl_src` varchar(64) DEFAULT NULL,
  `dl_dst` varchar(64) DEFAULT NULL,
  `nw_src` varchar(64) DEFAULT NULL,
  `nw_dst` varchar(64) DEFAULT NULL,
  `nw_proto` int(11) DEFAULT NULL,
  `tp_src` int(11) DEFAULT NULL,
  `tp_dst` int(11) DEFAULT NULL,
  `byte_count` DOUBLE,
  `packet_count` DOUBLE,
  `flow_count` DOUBLE,
    PRIMARY KEY(id)
)
