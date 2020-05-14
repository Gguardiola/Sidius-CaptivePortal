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
    print("                   Captive portal SETUP v3.2")
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


def LAMP_setup():

    print("============================================================")
    print("======================== LAMP SETUP ========================")
    print("============================================================")
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
    #os.system("mysql_secure_installation")
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
            print("Bye!!")
            sys.exit()

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
        print(Fore.YELLOW + "IF YOUR SUBNET MASK IS CLASSLESS INSTEAD OF CLASSFULL, LET THE FIELD EMPTY AND CHECK THE DOCUMENTATION ON [LINK]")
        print(Style.RESET_ALL) 
        internal_subnetmask = input("- ")   

        try:
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

    #named.conf.local
    f = open("setupTemplates/DNSzones","r")
    conflocal = f.read()
    conflocal = conflocal.replace("sample",domainName)
    conflocal = conflocal.replace("reversed",domain_reverse)
    f.close()
    f = open("setupTemplates/DNShandler","w")
    f.write(conflocal)
    f.close()   
#def DHCP_setup():



#def firewall_setup():




#def logs_setup():



#def permissions_setup():


presentation()
systemCheck()
dependences_setup()
LAMP_setup()
BIND_setup()
#hacer los archivos template en una carpeta para bind, dhcp...