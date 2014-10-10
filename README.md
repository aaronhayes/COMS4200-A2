COMP4200-A2
===========

Group Good COMS4200 Computer Networking II 2014. POX Application.


### 1. Mininet VM
* Setup a mininet VM, can be found at http://mininet.org/download/

### 2. Installing XAMP/LAMP With Repo
* Step 1: run the scripts/install-xampp.sh shell script and follow the prompts to install lampp
* Step 2: run the scripts/install-repo.sh shell script and follow the prompts to create ssh keys, clone repo.

### 3. Install POX component
* Run scripts/install-pox-co.sh shell script.


### 4. Run POX
* cd ~/pox
* ./pox.py [forwarding_component] stats 
* EXAMPLE: ./pox.py forwarding.hub stats
* If you need logs: ./pox.py log.level --DEBUG forwarding.hub stats
* Topo Discovery requires openflow.discovery and web.webcore
* EXAMPLE: ./pox log.level --DEBUG forwarding.hub web.webcore openflow.discovery topo stats

### 5. Run mininet
* Use whatever setup on mininet you like, ensure controller is set to remote.
* example: sudo mn --topo single,3 --mac --controller remote,ip=127.0.0.1,port=6633

### 6. Viewing Webpage
* Simply navigate to http://mininet-VM/web/
