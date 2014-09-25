# Should avoid using sudo unless its neccessary
# cd to home directory where we can freely write.
cd ~
# Download the installer. This is the 64-bit version as the mininet image is 64-bit.
wget http://downloads.sourceforge.net/project/xampp/XAMPP%20Linux/1.8.3/xampp-linux-x64-1.8.3-5-installer.run
# We wrote the file and own it so no need for sudo here. Make the file executable.
chmod +x xampp-linux-x64-1.8.3-5-installer.run
# Run the executable installer. Need higher permissions.
# Manually select yes (Y) for each option & hit enter each time.
sudo ./xampp-linux-x64-1.8.3-5-installer.run

# To Start Services
sudo /opt/lampp/lampp start

# To Stop Services
#sudo /opt/lampp/lampp stop

