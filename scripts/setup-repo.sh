# BASH script to clone git repo

cd
# Install git
apt-get git-core

#Generate public keys
ssh-keygen -t rsa -C "youremailaddresshere@iknowyouwontchangethis.com"
ssh-agent -s
ssh-add ~/.ssh/id_rsa

# COPY ENTIRE ~/.ssh/id_rsa content into ssh key on github.com

echo "Have you added your new ssh key to your github account? (Type 1 for Yes, 2 for No)"
select yn in "Yes" "No"; do
    case $yn in
        Yes ) break;;
        No) exit;;
    esac
done

# Clone Repeat
#apt-get git clone git@github.com:aaronhayes/COMP4200-A2.git

cd /opt/lampp/htdocs/

# Create link to web folder of repo 
sudo ln -s ~/COMS4200-A2/web/ web

# Now you can naviagte to http://mininet-VM/web/ (or use IP address) 
echo "now you can naviagate to http://mininet-VM/web/"