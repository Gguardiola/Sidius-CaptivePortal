<?php
$config = include "../config.php";
# sudo iptables Script
# Adjust those values right otherwise you will get locked out!
$EXTERNAL=$config["external_int"];
$INTERNAL=$config["internal_int"];
$EXTERNALNET=$config['external_subnet'];
$INTERNALIP=$config['internal_ip'];
$GATEWAYIP=$config['external_gateway']."/16";
$SSHIP="10.110.0.10/32";
$PAYMENTGATEWAY="sis-t.redsys.es";
# Adjust those values for DNS Forwarding
$DNSFORWARDER1=$config['dnsforwarder1'];
$DNSFORWARDER2=$config['dnsforwarder1'];

# Default Policy
exec("sudo iptables -X");
exec("sudo iptables -Z");
exec("sudo iptables -F");
exec("sudo iptables -t nat -X");
exec("sudo iptables -t nat -Z");
exec("sudo iptables -t nat -F");
exec("sudo iptables -P INPUT DROP");
exec("sudo iptables -P OUTPUT DROP");
exec("sudo iptables -P FORWARD DROP");

# Allow Loopback
exec("sudo iptables -A INPUT -i lo -j ACCEPT");
exec("sudo iptables -A OUTPUT -o lo -j ACCEPT");

# Server Rules
exec("# Anti-Port-Flood for ACCEPTED RULES");
exec("sudo iptables -A INPUT -p tcp -m connlimit --connlimit-above 10 -j REJECT --reject-with tcp-reset");
exec("sudo iptables -A INPUT -p tcp -m connlimit --connlimit-above 10 -j REJECT --reject-with tcp-reset");
# Enable NTP to specific address
exec("sudo iptables -A INPUT -i $EXTERNAL -p udp --dport 123 -j ACCEPT");
exec("sudo iptables -A OUTPUT -o $EXTERNAL -p udp --sport 123 -j ACCEPT");

# Enable Ping through all networks (only for testing purposes)
exec("sudo iptables -A INPUT -p icmp -j ACCEPT");
exec("sudo iptables -A OUTPUT -p icmp -j ACCEPT");
# Enable SSH to specific address
exec("sudo iptables -A INPUT -s $SSHIP -p tcp --dport 22 -j ACCEPT");
exec("sudo iptables -A OUTPUT -d $SSHIP -p tcp --sport 22 -j ACCEPT");

# Enable Server to query the specific DNS
exec("sudo iptables -A INPUT -p udp -i $EXTERNAL --sport 53 -s $DNSFORWARDER1 -j ACCEPT");
exec("sudo iptables -A INPUT -p udp -i $EXTERNAL --sport 53 -s $DNSFORWARDER2 -j ACCEPT");
exec("sudo iptables -A INPUT -p tcp -i $EXTERNAL --sport 53 -s $DNSFORWARDER1 -j ACCEPT");
exec("sudo iptables -A INPUT -p tcp -i $EXTERNAL --sport 53 -s $DNSFORWARDER2 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p udp -o $EXTERNAL --dport 53 -d $DNSFORWARDER1 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p udp -o $EXTERNAL --dport 53 -d $DNSFORWARDER2 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p tcp -o $EXTERNAL --dport 53 -d $DNSFORWARDER1 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p tcp -o $EXTERNAL --dport 53 -d $DNSFORWARDER2 -j ACCEPT");

# Enable DNS Serving to LAN
exec("sudo iptables -A INPUT -p udp --dport 53 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p udp --sport 53 -j ACCEPT");
exec("sudo iptables -A INPUT -p tcp --dport 53 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p tcp --sport 53 -j ACCEPT");
# Enable DHCP Serving to Clients
exec("sudo iptables -A INPUT -p udp --dport 67 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p udp --sport 67 -j ACCEPT");
exec("sudo iptables -A INPUT -p udp --dport 68 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p udp --sport 68 -j ACCEPT");
# HTTPS Enable Clients to Server
exec("sudo iptables -A INPUT -p tcp --dport 443 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p tcp --sport 443 -j ACCEPT");
# HTTP Enable Clients to Server
exec("sudo iptables -A INPUT -p tcp --dport 80 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p tcp --sport 80 -j ACCEPT");
#HTTPS Enable Server to Clients
exec("sudo iptables -A INPUT -p tcp --sport 443 -j ACCEPT");
exec("sudo iptables -A OUTPUT -p tcp --dport 443 -j ACCEPT");
# HTTP Enable Server to Clients
exec("sudo iptables -A INPUT -p tcp --sport 80 -j ACCEPT");
# Used for packages fetch
exec("sudo iptables -A OUTPUT -p tcp --dport 80 -j ACCEPT");
# Anti-DOS Policy for ACCEPTED RULES
exec("sudo iptables -A INPUT -m state --state INVALID -j DROP");
exec("sudo iptables -A FORWARD -m state --state INVALID -j DROP");
exec("sudo iptables -A OUTPUT -m state --state INVALID -j DROP");
# CAPTIVE PORTAL SETTINGS
# Forward toggle on
exec("echo 1 > /proc/sys/net/ipv4/ip_forward");
# Redirect all clients to DNS Server
exec("sudo iptables -t nat -A PREROUTING -i $INTERNAL -p udp --dport 53 -j DNAT --to-destination $INTERNALIP:53");
# In case of forwarded MAC, go to Internet (tunnel to Internet, no interaction with $EXTERNAL net
exec("#sudo iptables -t nat -A PREROUTING -i $INT -m mac --mac-source 08:00:27:f8:9d:80 -j ACCEPT");

#payment gateway and CDN access
exec("sudo iptables -A FORWARD -p tcp -d $PAYMENTGATEWAY -j ACCEPT");
exec("sudo iptables -t nat -A PREROUTING -p tcp -d $PAYMENTGATEWAY -j ACCEPT");

exec("sudo iptables -A FORWARD -p tcp -d fonts.gstatic.com -j ACCEPT");
exec("sudo iptables -t nat -A PREROUTING -p tcp -d fonts.gstatic.com -j ACCEPT");

exec("sudo iptables -A FORWARD -p tcp -d code.getmdl.io -j ACCEPT");
exec("sudo iptables -t nat -A PREROUTING -p tcp -d code.getmdl.io -j ACCEPT");

exec("sudo iptables -A FORWARD -p tcp -d stackpath.bootstrapcdn.com -j ACCEPT");
exec("sudo iptables -t nat -A PREROUTING -p tcp -d stackpath.bootstrapcdn.com -j ACCEPT");

exec("sudo iptables -A FORWARD -p tcp -d code.jquery.com -j ACCEPT");
exec("sudo iptables -t nat -A PREROUTING -p tcp -d code.jquery.com -j ACCEPT");

exec("sudo iptables -A FORWARD -p tcp -d cdn.jsdelivr.net -j ACCEPT");
exec("sudo iptables -t nat -A PREROUTING -p tcp -d cdn.jsdelivr.net -j ACCEPT");



#sudo iptables -A FORWARD -m mac --mac-source 08:00:27:f8:9d:80 ! -d $EXTERNALNET -i $INT -o $EXT -j ACCEPT
# In case of not forwarded MAC, go to Captive Portal
exec("sudo iptables -t nat -A PREROUTING -i $INTERNAL -p tcp --dport 1:65535 -j DNAT --to-destination $INTERNALIP");
exec("sudo iptables -t nat -A PREROUTING -i $INTERNAL -p udp --dport 1:65535 -j DNAT --to-destination $INTERNALIP");
# Bridge $EXTERNAL NET to $INTERNAL
exec("sudo iptables -A FORWARD -i $EXTERNAL -o $INTERNAL -j ACCEPT");
exec("sudo iptables -t nat -A POSTROUTING -o $EXTERNAL -j MASQUERADE");
?>