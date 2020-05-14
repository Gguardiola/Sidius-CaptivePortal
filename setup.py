import sys
import os
import time
import subprocess
import colorama
from colorama import Fore, Style
def presentation():
    print("")
    time.sleep(0.1)
    print("             __    _______ .___________. _______  __  ___ ")
    time.sleep(0.1)
    print("            |  |  /  _____||           ||   ____||  |/  / ")
    time.sleep(0.1)
    print("            |  | |  |  __  `---|  |----`|  |__   |  '  /  ")
    time.sleep(0.1)
    print("      .--.  |  | |  | |_ |     |  |     |   __|  |    <   ")
    time.sleep(0.1)
    print("      |  `--'  | |  |__| |     |  |     |  |____ |  .  \  ")
    time.sleep(0.1)
    print("       \______/   \______|     |__|     |_______||__|\__\ ")
    time.sleep(0.1)
    print("")                                                
    print("                   Captive portal SETUP v3.3")
    print("")
    print("Please verify that you comply with the following points before continuing.")   
    print("")  
    print("    ·   Internet connection")                  
    print("    ·   Two network interfaces")                  
    print("    ·   System updated and upgraded")   
    print("")               
    while(True):
        continueChecker = input("Continue?[y/n]: ")
        continueChecker = continueChecker.lower()
        if continueChecker == "y":
            return True
        elif continueChecker == "n":
            print("Bye!!")
            sys.exit()

def installationChecker(checker,serviceName):
        
    if "/" in checker:
        print("")
        print(Fore.GREEN + serviceName+" ------------------- OK")
        print(Style.RESET_ALL)
    else:
        print("")
        print(Fore.RED + "FAILED INSTALLING "+serviceName+", CHECK YOUR INTERNET CONNECTION!")
        print(Style.RESET_ALL) 
        sys.exit()   

def systemCheck():

    print("==================================================================")
    print("======================== SYSTEM CHECK ============================")
    print("==================================================================")

    ##CHECKING IF THE SCRIPT IS RUNNING ON ROOT
    rootcheck = subprocess.getoutput("touch /etc/rootcheck")

    if "denied" in rootcheck:
        print("")
        print(Fore.RED + "PLEASE MAKE SURE THAT YOU RUN THIS SETUP WITH ROOT PRIVILEGES!!")    
        print(Style.RESET_ALL) 
        sys.exit()

    else:
        os.system("rm /etc/rootcheck")
        print("")
        print(Fore.GREEN + "SETUP RUNNING WITH ROOT -------------------------- OK")
        print(Style.RESET_ALL)

    #CHECKING IF THE MACHINE HAVE 2 OR MORE NETWORK INTERFACES (EXCLUDING LOOKBACK INT)
    intcheck = subprocess.getoutput("ifconfig -a | grep 'flags' | wc -l");intcheck = int(intcheck)
    if intcheck >= 3:
        print("")
        print(Fore.GREEN + "MORE THAN 2 NETWORK INTERFACES ------------------- OK")
        print(Style.RESET_ALL)


    else:
        print("")
        print(Fore.RED + "YOU NEED AT LEAST TWO NETWORK INTERFACES!")
        print(Style.RESET_ALL) 
        sys.exit()

def dependences_setup():

    print("===================================================================")
    print("======================== DEPENDENCES SETUP ========================")
    print("===================================================================")
    print("")
    print(Fore.BLUE + "Installing vnstat...")
    print(Style.RESET_ALL) 
    os.system("apt-get install vnstat")
    print("")
    print(Fore.BLUE + "Installing ifstat...")
    print(Style.RESET_ALL) 
    os.system("apt-get install ifstat")
    print("")

    serviceName = "vnstat"
    checker = subprocess.getoutput("whereis "+ serviceName)
    installationChecker(checker,serviceName)
    
    serviceName = "ifstat"
    checker = subprocess.getoutput("whereis "+ serviceName)
    installationChecker(checker,serviceName)

    print("")
    print(Fore.GREEN + "DEPENDENCES DONE!")
    print(Style.RESET_ALL)
    print("")

def LAMP_setup():

    print("============================================================")
    print("======================== LAMP SETUP ========================")
    print("============================================================")
    print("")
    while(True):
        continueChecker = input("Do you want to install and configure a LAMP server?[y/n]: ")
        continueChecker = continueChecker.lower()
        if continueChecker == "y":
            break
        elif continueChecker == "n":
            print("")
            print(Fore.BLUE + "Skipping LAMP setup...")
            print(Style.RESET_ALL) 
            return False

    print("")
    print(Fore.BLUE + "Installing Apache2...")
    print(Style.RESET_ALL) 
    os.system("apt-get install apache2")
    os.system("cat setupTemplates/dir.conf > /etc/apache2/mods-enabled/dir.conf")
    print("")
    print(Fore.BLUE + "Installing MySQL...")
    print(Style.RESET_ALL) 
    os.system("apt-get install mysql-server mysql-client")
    print("")
    os.system("mysql_secure_installation")
    print("")
    print(Fore.BLUE + "Installing PHP...")
    print(Style.RESET_ALL) 
    os.system("apt-get install php libapache2-mod-php php-mysql php-cli")
    print("")

    serviceName = "apache2"
    checker = subprocess.getoutput("whereis "+ serviceName)
    installationChecker(checker,serviceName)

    serviceName = "mysql"
    checker = subprocess.getoutput("whereis "+ serviceName)
    installationChecker(checker,serviceName)

    serviceName = "php"
    checker = subprocess.getoutput("whereis "+ serviceName)
    installationChecker(checker,serviceName)   

    print("")
    print(Fore.GREEN + "LAMP DONE!")
    print(Style.RESET_ALL)
    print("")

def BIND_setup():

    print("===========================================================")
    print("======================== DNS SETUP ========================")
    print("===========================================================")
    print("")
    while(True):
        continueChecker = input("Do you want to configure a Domain name?[y/n]: ")
        continueChecker = continueChecker.lower()
        if continueChecker == "y":
            break
        elif continueChecker == "n":
            print("")
            print(Fore.BLUE + "Skipping DNS setup...")
            print(Style.RESET_ALL) 
            return False

    print("")
    print(Fore.BLUE + "Installing BIND9...")
    print(Style.RESET_ALL) 
    os.system("apt-get install bind9")
    print("")

    serviceName = "bind9"
    checker = subprocess.getoutput("whereis "+ serviceName)
    installationChecker(checker,serviceName)   
    while(True):
        print("")
        print("Domain name:")
        print("Example: elPratAirport.webredirect.org")
        domainName = input("- ")

        print("")
        print("Internal network interface IP Address:")
        print("Example: 10.110.0.1")
        internal_ip = input("- ")    

        print("")
        print("Internal network interface Subnet Mask:")
        print("Example: /16")
        print(Fore.YELLOW + "IF YOUR SUBNET MASK IS CLASSLESS INSTEAD OF CLASSFULL, LEAVE THE FIELD EMPTY AND CHECK THE DOCUMENTATION ON [LINK]")
        print(Style.RESET_ALL) 
        internal_subnetmask = input("- ")   

        try:
            if "/" not in internal_subnetmask and len(internal_subnetmask) > 0:
                print("")
                print(Fore.RED + "FAILED! YOU TYPED WRONG ONE OF THE FIELDS! PLEASE FOLLOW THE EXAMPLE FORMAT!")
                print(Style.RESET_ALL)      

            else:
                domain_reverse = internal_ip.split(".");domain_reverse = domain_reverse[1]+"."+domain_reverse[0]+".in-addr.arpa"
                break
        except:
            print("")
            print(Fore.RED + "FAILED! YOU TYPED WRONG ONE OF THE FIELDS! PLEASE FOLLOW THE EXAMPLE FORMAT!")
            print(Style.RESET_ALL) 
            
    
    print("")
    print(Fore.BLUE + "Updating BIND9 files...")
    print(Style.RESET_ALL)

    #DIRECT ZONE
    f = open("setupTemplates/direct.db","r")
    direct = f.read()
    direct = direct.replace("sample",domainName)
    direct = direct.replace("internal_ip",internal_ip)
    f.close()
    f = open("setupTemplates/DNShandler","w")
    f.write(direct)
    f.close()

    os.system("cat setup/Templates/DNShandler > /etc/bind/direct.db")

    #REVERSE ZONE
    internal_ip = internal_ip.split(".")
    classless = False
    if internal_subnetmask == "/8":
        ip_reverseZone = internal_ip[1]+"."+internal_ip[2]+"."+internal_ip[3]    

    elif internal_subnetmask == "/16":
        ip_reverseZone = internal_ip[2]+"."+internal_ip[3]
    
    elif internal_subnetmask == "/24":
        ip_reverseZone = internal_ip[3]

    else:
        classless = True

    f = open("setupTemplates/reverse.db","r")
    reverse = f.read()
    reverse = reverse.replace("sample",domainName)
    if classless == False:
        reverse = reverse.replace("internal_ip",ip_reverseZone)
    f.close()
    
    f = open("setupTemplates/DNShandler","w")
    f.write(reverse)
    f.close()

    os.system("cat setup/Templates/DNShandler > /etc/bind/reverse.db")

    #named.conf.local
    f = open("setupTemplates/DNSzones","r")
    conflocal = f.read()
    conflocal = conflocal.replace("sample",domainName)
    conflocal = conflocal.replace("reversed",domain_reverse)
    f.close()
    f = open("setupTemplates/DNShandler","w")
    f.write(conflocal)
    f.close()   

    os.system("cat setup/Templates/DNShandler > /etc/bind/named.conf.local")    

    #named.conf.options
    ###WIP####
    #ADD OPTIONAL PROXY DNS
    f = open("setupTemplates/DNSforwarders","r")
    forwarders = f.read()
    f = open("setupTemplates/DNShandler","w")
    f.write(forwarders)
    f.close()   

    os.system("cat setup/Templates/DNShandler > /etc/bind/named.conf.options")
    print("")
    print(Fore.GREEN + "DNS DONE!")
    print(Style.RESET_ALL)
    print("")

def DHCP_setup():
    print("============================================================")
    print("======================== DHCP SETUP ========================")
    print("============================================================")
    print("")
    while(True):
        continueChecker = input("Do you want to configure the DHCP?[y/n]: ")
        continueChecker = continueChecker.lower()
        if continueChecker == "y":
            break
        elif continueChecker == "n":
            print("")
            print(Fore.BLUE + "Skipping DHCP setup...")
            print(Style.RESET_ALL) 
            return False
    print("")
    print(Fore.BLUE + "Installing isc-dhcp-server...")
    print(Style.RESET_ALL) 
    os.system("apt-get install isc-dhcp-server")
    print("")

    serviceName = "dhcpd"
    checker = subprocess.getoutput("whereis "+ serviceName)
    installationChecker(checker,serviceName)   

    print("")
    print("DHCP SUBNET")
    print("Internal network subnet IP address:")
    print("Example: 10.110.0.0")
    subnetIP = input("- ")    

    print("")
    print("Internal network subnet mask:")
    print("Example: 255.255.0.0")
    subnetMASKIP = input("- ")    

    print("")
    print("Internal network interface IP address:")
    print("Example: 10.110.0.1")
    gatewayIP = input("- ")        

    print("")
    print("DHCP RANGE")
    print("First IP address:")
    print("Example: 10.110.0.20")
    firstRangeIP = input("- ")        

    print("")
    print("Last IP address:")
    print("Example: 10.110.0.254")
    lastRangeIP = input("- ") 

    f = open("setupTemplates/DHCPrange","r")
    dhcpfile = f.read()
    dhcpfile = dhcpfile.replace("subnetIP",subnetIP)
    dhcpfile = dhcpfile.replace("subnetMASKIP",subnetMASKIP)
    dhcpfile = dhcpfile.replace("gatewayIP",gatewayIP)
    dhcpfile = dhcpfile.replace("firstRangeIP",firstRangeIP)    
    dhcpfile = dhcpfile.replace("lastRangeIP",lastRangeIP)

    f.close()
    f = open("setupTemplates/DHCPhandler","w")
    f.write(dhcpfile)
    f.close()


    os.system("cat setup/Templates/DHCPhandler > /etc/dhcp/dhcpd.conf")


    print("")
    print(Fore.GREEN + "DHCP DONE!")
    print(Style.RESET_ALL)
    print("")    

def firewall_setup():
    print("================================================================")
    print("======================== FIREWALL SETUP ========================")
    print("================================================================")
    print("")
    print(Fore.BLUE + "Giving Apache2 privileges to run iptables commands...")
    print(Style.RESET_ALL) 
    sudoers = subprocess.getoutput("cat /etc/sudoers")
    sudoers += "\nwww-data ALL=NOPASSWD: /sbin/iptables"
    f = open("setupTemplates/sudoers","w")
    f.write(sudoers)
    f.close()
    os.system("cat setupTemplates/sudoers > /etc/sudoers")

    print("")
    print(Fore.BLUE + "Setting up iptables rules...")
    print(Style.RESET_ALL)    

    print("")
    print("External network interface name:")
    print("Example: enp0s3")
    externalINT = input("- ")    

    print("")
    print("Internal network interface name:")
    print("Example: enp0s8")
    internalINT = input("- ")    

    while(True):
        print("")
        print("External network subnet IP address (with subnet mask):")
        print("Example: 10.110.0.0/16")
        externalSUBNETIP = input("- ")  

        if "/" not in externalSUBNETIP:
            print("")
            print(Fore.RED + "FAILED! YOU TYPED WRONG THE FIELD! PLEASE FOLLOW THE EXAMPLE FORMAT!")
            print(Style.RESET_ALL) 
        else:
            break

    print("")
    print("Internal network interface IP address:")
    print("Example: 10.110.0.1")
    internalIP = input("- ")        

    while(True):
        print("")
        print("External network default gateway (with subnet mask):")
        print("Example: 10.110.0.1/16")
        externalGATEWAY = input("- ") 
        if "/" not in externalSUBNETIP:
            print("")
            print(Fore.RED + "FAILED! YOU TYPED WRONG THE FIELD! PLEASE FOLLOW THE EXAMPLE FORMAT!")
            print(Style.RESET_ALL) 
        else:
            break    

    print("")
    print("ip of the single host that can connect via SSH :")
    print(Fore.YELLOW + "IF YOU DON'T WANT ANYONE TO CONNECT VIA SSH, LEAVE THE FIELD EMPTY")
    print(Style.RESET_ALL) 
    print("Example: 10.110.0.10")
    sshHOST = input("- ")    

    if len(sshHOST) == 0:
        sshHOST = "127.0.0.1"
    sshHOST += "/32"

    print("")
    print("Primary DNS forwarder:")
    print("Example: 8.8.8.8")
    dnsforwarder1 = input("- ")         

    print("")
    print("Secondary DNS forwader:")
    print("Example: 8.8.4.4")
    dnsforwarder2 = input("- ")         

    print("")
    print(Fore.YELLOW + "THE DEFAULT PAYMENT GATEWAY IS REDSYS, IF YOU WANT TO CHANGE THIS, PLEASE CHECK DE DOCUMENTATION IN [LINK]")
    print(Style.RESET_ALL)
    f = open("setupTemplates/iptablesTemplate","r")
    iptablesconf = f.read()
    iptablesconf = iptablesconf.replace("externalINT",externalINT)
    iptablesconf = iptablesconf.replace("internalINT",internalINT)
    iptablesconf = iptablesconf.replace("externalSUBNETIP",externalSUBNETIP)
    iptablesconf = iptablesconf.replace("internalIP",internalIP)
    iptablesconf = iptablesconf.replace("externalGATEWAY",externalGATEWAY)
    iptablesconf = iptablesconf.replace("sshHOST",sshHOST)
    iptablesconf = iptablesconf.replace("dnsforwarder1",dnsforwarder1)
    iptablesconf = iptablesconf.replace("dnsforwarder2",dnsforwarder2) 
    f.close()
    f = open("setupTemplates/iptablesHandler","w")
    f.write(iptablesconf)
    f.close()

    os.system("cat setupTemplates/iptablesHandler > ../cpanel/firewall.sh")
    os.system("chmod +x ../cpanel/firewall.sh")
    print("")
    print(Fore.GREEN + "FIREWALL DONE!")
    print(Style.RESET_ALL)
    print("")   

    while(True):
        continueChecker = input("Do you want to run now the firewall?[y/n]: ")
        continueChecker = continueChecker.lower()
        if continueChecker == "y":
            break
        elif continueChecker == "n":
            print("")
            print(Fore.BLUE + "Skipping. You can manually run the firewall going to the /cpanel folder and executing ./firewall.sh.")
            print(Style.RESET_ALL) 
            return False

def logs_setup():
    print("============================================================")
    print("======================== LOGS SETUP ========================")
    print("============================================================")
    print("")
    time.sleep(0.2)

    print(Fore.BLUE + "Making the captiveportal.log...")
    print(Style.RESET_ALL) 
    os.system("touch /var/log/captiveportal.log")   
    print("")
    print(Fore.GREEN + "DONE!")
    print(Style.RESET_ALL)
    time.sleep(0.2)

    print("")
    print(Fore.BLUE + "Making the iptablesRules.log...")
    print(Style.RESET_ALL) 
    os.system("touch /var/log/iptablesRules.log")   
    print("")
    print(Fore.GREEN + "DONE!")
    print(Style.RESET_ALL)
    time.sleep(0.2)

def permissions_setup():
    print("===================================================================")
    print("======================== PERMISSIONS SETUP ========================")
    print("===================================================================")
    print("")
    time.sleep(0.2)
    print(Fore.BLUE + "Removing Apache2 directory index from the browser...")
    print(Style.RESET_ALL) 
    apache2conf = subprocess.getoutput("cat /etc/apache2/apache2.conf")
    apache2conf = apache2conf.replace("Indexes","")
    f = open("setupTemplates/apache2.conf","w")
    f.write(apache2conf)   
    f.close()
    os.system("cat setupTemplates/apache2.conf > /etc/apache2/apache2.conf")
    print("")
    print(Fore.GREEN + "DONE!")
    print(Style.RESET_ALL)
    time.sleep(0.2)

    print("")
    print(Fore.BLUE + "Giving to apache2 permissions to write on config.php...")
    print(Style.RESET_ALL) 
    os.system("chown www-data:www-data ../config.php")
    print("")
    print(Fore.GREEN + "DONE!")
    print(Style.RESET_ALL)
    time.sleep(0.2)

    print("")
    print(Fore.BLUE + "Giving to apache2 permissions to write on captiveportal.log...")
    print(Style.RESET_ALL) 
    os.system("chown www-data:www-data /var/log/captiveportal.php")
    print("")
    print(Fore.GREEN + "DONE!")
    print(Style.RESET_ALL)
    time.sleep(0.2)

    print("")
    print(Fore.BLUE + "Giving to apache2 permissions to write on iptablesRules.log...")
    print(Style.RESET_ALL) 
    os.system("chown www-data:www-data /var/log/iptablesRules.php")    
    print("")
    print(Fore.GREEN + "DONE!")
    print(Style.RESET_ALL)    
    time.sleep(0.2)

def goodbye():
    print("")
    print(Fore.BLUE + "Restarting the services...")
    print(Style.RESET_ALL) 
    os.system("service apache2 restart")
    os.system("service bind9 restart")
    os.system("service isc-dhcp-server restart")    
    print("")
    print(Fore.GREEN + "CONGRATULATIONS! EVERYTHING IS DONE.")
    print("PLEASE, OPEN A BROWSER AND GO TO yourdomain.org/first_run.php TO FINISH THE INSTALLATION.")
    print(Style.RESET_ALL)       

presentation()
systemCheck()
dependences_setup()
LAMP_setup()
BIND_setup()
DHCP_setup()
firewall_setup()
logs_setup()
permissions_setup()
goodbye()
