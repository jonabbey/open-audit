#!/usr/bin/perl -w
# (c) 2009 Chad Sikorra, chad.sikorra@gmail.com

# PerlSvc needed for Windows service, Win32::Daemon has issues with forking
package PerlSvc;

use strict;
use POSIX 'setsid';
use POSIX ":sys_wait_h";
use Carp;
use Getopt::Long;
use FindBin qw($Bin $Script);
use lib "$Bin/../lib/perl";
our $VERSION = '0.20';

# Some global variables needed
our $WINDOWS_SERVER = ( $^O =~ m/mswin32|winxp/i ) ? ( '1' ) : ( '0' );
our $AES_KEY = Get_AES_Key();            # AES key to decrypt some DB info
our $DAEMON;                             # Set to 1 if the script runs as a daemon with schedules
our %SCHEDULE_PIDS;                      # PID info hash for schedules being managed
our $LOOP_PID;                           # Track the PID of the main loop for killing schedules
our $CHILD_PID;                          # PID from a child that may need to be killed on timeout
our %Config;                             # Hash options for PerlSvc for installing the service
our $SERVICE_INSTALL;                    # Set to 1 if the --instal switch was used with PerlSvc
our $URL;                                # The base URL to Open-Audit

# Trap a SIGINT/SIGTERM/SIGBREAK and run Cron_Cleanup
$SIG{INT  } = \&Cron_Cleanup;
$SIG{TERM } = \&Cron_Cleanup;
$SIG{BREAK} = \&Cron_Cleanup if ( $WINDOWS_SERVER );

# Let the kernel reap forked processes
$SIG{CHLD} = 'IGNORE';

############################################
#             Usage Information            #
############################################

sub Usage {
  print <<EOF;
Usage: $0 [options] ...

This script is used by Open-AudIT to start audit schedules or audits.
It can be controlled through the front-end web interface.

    Options:
      --help                    Show this information and exit
      --version                 Print the version and exit
      --daemon                  Run the script in daemon mode
      --check-pid               Check if the daemon PID exists or not
      --cron-start              Start the Web-Schedule service to manage schedules
      --cron-stop               Stop the Web-Schedule service and end running schedules
    Windows:
      --install                 Install as a Windows service [ exe version only ]
      --service-custom          Prompt for information when used with --install
EOF
  exit 0;
}

############################################
#        Windows Only Service Subs         #
############################################

############################################################
# Purpose  : These subs handle switches/events with PerlSvc
# Comments : sub Help    = triggered with --help switch
#            sub Install = triggered with --install switch
#            sub Startup = Called when started as a service

sub Help    { Usage();                             }
sub Install { $SERVICE_INSTALL = 1; Interactive(); }
sub Startup { Win32_Service();                     }

############################################################
# Purpose : Install this script as a service on Windows
# Usage   : Called using the switch --service-install
# Returns : Exit with 0 on success, 1 otherwise

sub Service_Install {
  my $custom = shift; 
  my ( $c_user, $c_pass, $c_name );

  exit 1 if ( not $WINDOWS_SERVER );

  my $path  = ( $Script =~ m/\.pl$/i ) ? ( $^X ) : ( $Bin );
  my $param = ( $Script =~ m/\.pl$/i ) ? ( "\"$Bin\\$Script\" --service-start" ) : ( "--service-start" );

  if ( $custom ) {
    my $confirm;
    use Term::ReadKey;

    print "Leave an answer blank to accept a default value\n\n";
    print "User format for domain account: DomainName\\UserName\n";
    print "User format for local account: .\\UserName\n\n";
    print "Service name [openaudit]: ";
    chomp( $c_name = <> );

    print "Run service as user [local system]: ";
    chomp( $c_user = <> );

    if ( $c_user ) {
      while ( 1 ) {
        print "Password for $c_user: ";
        ReadMode 'noecho';
        chomp( $c_pass  = ReadLine 0 );
        print "\n";
        print "Confirm password: ";
        chomp( $confirm = ReadLine 0 );
        print "\n";
        ReadMode 'normal';
        last if ( $c_pass eq $confirm );
        print "Passwords don't match, please re-enter\n";
      }
    }
  }

  my $name = ( $c_name ) ? ( $c_name ) : ( 'openaudit' );

  $Config{'StartNow'   } = 0;
  $Config{'Description'} = 'Open-AudIT Web-Schedule Service';
  $Config{'ServiceName'} = ( $c_name ) ? ( $c_name ) : ( 'openaudit' );
  $Config{'DisplayName'} = ( $c_name ) ? ( $c_name ) : ( 'openaudit' );
  $Config{'UserName'   } = $c_user if ( $c_user );
  $Config{'Password'   } = $c_pass if ( $c_pass );
}

############################################################
# Purpose : Start the script as a service, granted that it
#           got here from being called by the service manager
# Usage   : Called by Startup() when PerlSvc receives a service
#           start control
# Returns : Finishes with Cron_Cleanup when the service is
#           issued a stop/shutdown control

sub Win32_Service {
  exit 1 if ( not $WINDOWS_SERVER );

  eval {
    require Schedule::Cron;
    Schedule::Cron->import;
  };

  if ( $@ ) {
    Log_Cron("ERROR: Schedule::Cron module missing");
    exit 1;
  }

  # Set the PID to track this process for clean-up
  $LOOP_PID = $$; 
  $DAEMON   = 1;

  my %cron_cfg  = %{ Get_Audit_Settings() };

  # Do PID check/update
  Start_Daemon_Check();

  # The service starts here, breaks the loop on a shutdown
  while ( ContinueRun($cron_cfg{'poll_interval'}) ) {
    Poll_Database();
  }

  Cron_Cleanup();
}

############################################
#                 Test Subs                #
############################################

############################################################
# Purpose : See that LDAP/MySQL queries work correctly
# Usage   : Internally by sub name. Called via ajax from
#           audit_configuration.php
# Returns : A list of computers on success, exit 1 otherwise

sub Test_Query {
  my $config_id = shift;
  my @computers = Get_Computer_List($config_id);

  if ( @computers ) {
    print "Computers returned: " . scalar(@computers) . "\n\n";
    foreach my $computer ( sort @computers ) {
      print $computer . "\n";
    }
    exit 0;
  }
  else {
    print "No computers returned by query\n";
    exit 1;
  }
}

############################################################
# Purpose : See that NMAP works correctly
# Usage   : Internally by sub name. Called via ajax from
#           audit_configuration.php
# Returns : The working status of NMAP

sub Test_Nmap {
  my $config_id = shift;
  my $cfg = Get_Config_Info($config_id);

  my $cmd = 
    ( $WINDOWS_SERVER ) ?
    ( "\"$$cfg{'nmap_path'}\" -O localhost"           ) :
    ( "sudo \"$$cfg{'nmap_path'}\" -O localhost 2>&1" ) ;

  my $out = `$cmd`;

  if ( $out =~ m/\[sudo\] password for ([-\w]+):.*/i ) {
    print  "The user '$1' cannot run sudo!\n"
          ."Run the command 'visudo' at a shell prompt and add the following line...\n\n"
          ."$1=NOPASSWD: $$cfg{'nmap_path'}\n\n";
  }
  elsif ( $out =~ m/sudo: command not found/i ) {
    print  "Cannot find sudo!\n"
          ."Sudo is needed for port scans when using Linux\n\n";
  }

  print "Nmap command output was:\n\n $out";
}

############################################
#            Read OA PHP Config            #
############################################

############################################################
# Purpose : Get config from include_config.php
# Usage   : Internally by sub name
# Returns : include_config vars/values as a hash key/value

sub Read_Config {
  my %include_config;

  open my $oa_config, "$Bin/../include_config.php" or croak "Unable to Open Config: $!";

  while (<$oa_config>) {
    if ( m/^\$(\w+) = '(.*)'/i ) {
      $include_config{"$1"} = $2;
    }
  }

  close $oa_config;

  return %include_config;
}

############################################
#             MySQL Functions              #
############################################

############################################################
# Purpose  : Mimick GetAesKey from include_functions.php
# Usage    : Internally by sub name
# Returns  : The AES key for some OA DB fields
# Comments : OS guess may be different due to PHP specific 
#            vars used in OA

sub Get_AES_Key {
  my ( $aes_key, $out ) = ( "openaudit", undef );

  if ( $WINDOWS_SERVER ) {
    $out = `vol c:`;
    $aes_key = ( $out =~ /\b(\w+-\w+)$/ ) ? "$1" : "openaudit";
  }
  elsif ( "$^O" =~ /linux/i ) {
    $out = `ls /dev/disk/by-uuid | sort | head -1`;
    chomp( $aes_key = $out );
  }

  return $aes_key;
}

############################################################
# Purpose : Execute SQL against the OA DB
# Usage   : Internally by sub name
#             Arg 1 : SQL statement
#             Arg 2 : Query type (select/non_select)
# Returns : Bare return on non_select fail, HoH's on select

sub MySQL_Execute {
  my ( $sql, $query_type ) = @_;
  my %include_config = Read_Config();

  eval {
    require DBI;
    DBI->import;
  };

  if ( $@ ) {
    print "ERROR: DBI module needed to query database\n";
    exit 1;
  }

  my $dbh = DBI->connect(
    "dbi:mysql:$include_config{'mysql_database'}:$include_config{'mysql_server'}:3306",
               $include_config{'mysql_user'},$include_config{'mysql_password'},
               { RaiseError => 1, AutoCommit => 1 }
  );

  if ( $query_type eq "non_select" ) {
    $dbh->do($sql) ? return 1 : return;
  }
  elsif ( $query_type eq "select" ) {
    return $dbh->selectall_hashref($sql, 1); # Use 1st col for HoH key
  }
}

############################################################
# Purpose : Make sure log message is escaped for mysql
# Usage   : Called by Log_Audit and Log_Cron
#             Arg 1 : Message to escape
# Returns : The escaped message

sub MySQL_Quote_String {
  my ( $msg ) = shift;
  my %include_config = Read_Config();

  eval {
    require DBI;
    DBI->import;
  };

  if ( $@ ) {
    print "ERROR: DBI module needed to query database\n";
    exit 1;
  }

  my $dbh = DBI->connect(
    "dbi:mysql:$include_config{'mysql_database'}:$include_config{'mysql_server'}:3306",
               $include_config{'mysql_user'},$include_config{'mysql_password'},
               { RaiseError => 1, AutoCommit => 1 }
  );

  return $dbh->quote($msg);
}

############################################################
# Purpose : Trigger an email by POSTing to a PHP page
# Usage   : Called if a scheule has email logs enabled
#             Arg 1 : The timestamp for the audit log entries
#             Arg 2 : The schedule ID to send logs for
# Returns : Nothing to return

sub Send_Audit_Log_Email {
  my ( $ts , $schedule_id ) = @_;
  my %ws_cfg = %{ Get_Audit_Settings() };
  my %s_cfg  = %{ Get_Schedule_Info($schedule_id) };

  require LWP::UserAgent;
  LWP::UserAgent->import;

  # Post to the webpage to trigger an email
  my $ua       = LWP::UserAgent->new();
  my $response = $ua->post(
    $ws_cfg{'base_url'} . 'admin_email_trigger.php',
    [
      'schedule_id' => $schedule_id,
      'timestamp'   => $ts,
    ]
  );

  if ( not $response->is_success) {
    my $msg  = "ERROR: Unable to send email for schedule \"$s_cfg{'name'}\". ";
       $msg .= "Server Response - " . $response->status_line;
    Log_Cron($msg);
  }

  exit;
}

############################################
#            Audit Sub Routines            #
############################################

############################################################
# Purpose : Get a list of computers from a given config
# Usage   : Called internally
#             Arg 1 : config id
# Returns : A list of computers/ip addresses

sub Get_Computer_List {
  my $config_id = shift;
  my $cfg = Get_Config_Info($config_id);

  my %list = (
    'domain'  => sub { return Get_LDAP_Computers($cfg)             },
    'list'    => sub { return split /\n/, $$cfg{'pc_list'}         },
    'iprange' => sub { return Get_IP_Range_Computers($cfg)         },
    'mysql'   => sub { return Get_MySQL_Computers($config_id,$cfg) },
  );

  return $list{ $$cfg{'audit_type'} }->();
}

############################################################
# Purpose : Parse config and return options hash
# Usage   : Called from any sub that needs to know options
#             Arg 1 : config id
# Returns : A hash of options for the audit config

sub Get_Config_Info {
  my $cfg_id = shift;
  my ( $nmap_args, $nmap_path, $com_path );
  my %ws_cfg  = %{ Get_Audit_Settings() };
  my ( $ldap_user, $ldap_pass, $ldap_server, $ldap_path, $ldap_fqdn ) = undef;

  my $sql = "SELECT * FROM audit_configurations WHERE audit_cfg_id = '$cfg_id'";
  my %cfg = %{ MySQL_Execute($sql,"select") };

  my $action   = $cfg{$cfg_id}{'audit_cfg_action'};
  my $cfg_name = $cfg{$cfg_id}{'audit_cfg_name'  };

  # The credentials for doing the audits
  my ( $audit_user, $audit_pass ) =
    (  $cfg{$cfg_id}{ 'audit_cfg_audit_use_conn' }  == 0                          ) ?
    (  Get_Config_Auth($cfg_id)                                                   ) :
    (  LDAP_Connections_Info( $cfg{$cfg_id}{'audit_cfg_audit_conn'},'audit_user') ) ;

  # Format the username if this is a windows audit/command
  if ( $cfg{$cfg_id}{'audit_cfg_os'} eq 'windows' and $action =~ m/command|pc/ ) {
    # Username needs to be in the form "domain\\user" or just "user"
    if ( $audit_user =~ m/(.*)\@(.*)\.\w+$/ ) {
      my ( $user , $domain ) = ( $1 , $2 );
      $audit_user = ( not $WINDOWS_SERVER ) ? ( "$domain\\\\$user" ) : ( "$domain\\$user" );
    }
    elsif ( $audit_user =~ m/^.*\\.*$/ and not $WINDOWS_SERVER ) {  # Escapes for linux shell...
      $audit_user =~ s/\\/\\\\/;
    }
  }

  # Format the VBS path
  $cfg{$cfg_id}{'audit_cfg_win_vbs'} =~ s/\//\\/g;
  $cfg{$cfg_id}{'audit_cfg_win_vbs'} =~ s/\\/\\\\/g if ( not $WINDOWS_SERVER );

  $nmap_path = 
    ( not $cfg{$cfg_id}{'audit_cfg_nmap_path'} ) ?
    ( Get_File_Path('nmap')                    ) :
    ( $cfg{$cfg_id}{'audit_cfg_nmap_path'}     ) ;

  # If this is Windows, check the default install location for Nmap
  if ( not defined $nmap_path and $WINDOWS_SERVER ) {
    if ( -e "$ENV{'ProgramFiles'}\\Nmap\\nmap.exe" ) {
      $nmap_path = "$ENV{'ProgramFiles'}\\Nmap\\nmap.exe";
    }
  }

  my $com_bin = ( $WINDOWS_SERVER ) ? ( 'RemCom.exe' ) : ( 'winexe' );

  # Get the path to winexe or RemCom.exe
  if ( not $cfg{$cfg_id}{'audit_cfg_com_path'} ) {
    my $fs = ( $WINDOWS_SERVER ) ? ( '\\' ) : ( '/' );
    $com_path = 
      ( not -e "$Bin$fs$com_bin" ) ?
      ( Get_File_Path($com_bin) or warn("Unable to locate $com_bin") ) :
      ( "$Bin$fs$com_bin" ) ;
  }
  else {
    $com_path = $cfg{$cfg_id}{'audit_cfg_com_path'};
  }

  # String together the nmap options.
  $nmap_args .= 
    ( $cfg{$cfg_id}{'audit_cfg_nmap_tcp_syn'} == '1' ) ? ( ' -sS' ) : ( '' ) ;
  $nmap_args .= 
    ( $cfg{$cfg_id}{'audit_cfg_nmap_udp'    } == '1' ) ? ( ' -sU' ) : ( '' ) ;
  $nmap_args .=
    ( $cfg{$cfg_id}{'audit_cfg_nmap_srv'    } == '1'                 ) ?
    ( " -sV --version-intensity $cfg{$cfg_id}{'audit_cfg_nmap_int'}" ) :
    ( ''                                                             ) ;
  $nmap_args .= " --host-timeout $cfg{$cfg_id}{'audit_cfg_wait_time'}s";

  # Verify nmap path
  if ( $action eq 'nmap' ) {
    if ( not defined $nmap_path or not -e "$nmap_path" ) { 
      Log_Cron("ERROR: Unable to Locate Nmap for Config - $cfg{$cfg_id}{'audit_cfg_name'}");
      exit 1;
    }
  }

  # Verify winexe/remcom.exe path for Windows audits
  if ( $action eq 'pc' and $cfg{$cfg_id}{'audit_cfg_os'} eq 'windows' ) {
    if ( not -e "$com_path" ) { 
      Log_Cron("ERROR: Cannot Locate $com_bin for Config - $cfg_name");
      exit 1;
    }
  }

  # Grab username/pass from the database, decrypting it this time
  $sql = "SELECT audit_cfg_id,
                    AES_DECRYPT(audit_cfg_ldap_user,'$AES_KEY') AS ldap_user,
                    AES_DECRYPT(audit_cfg_ldap_pass,'$AES_KEY') AS ldap_pass
             FROM audit_configurations WHERE audit_cfg_id = '$cfg_id'";
  my %auth_info = %{ MySQL_Execute($sql,"select") };

  # Set up variables based on if a LDAP connection table entry is used or not
  if ( $cfg{$cfg_id}{'audit_cfg_type'} eq 'domain' ) {
    ( $ldap_user, $ldap_pass, $ldap_server, $ldap_path, $ldap_fqdn ) =
      (  $cfg{$cfg_id}{ 'audit_cfg_ldap_use_conn' }  == 0 ) ?
      (  $auth_info{$cfg_id}{'ldap_user'},
         $auth_info{$cfg_id}{'ldap_pass'},
         $cfg{$cfg_id}{'audit_cfg_ldap_server'},
         $cfg{$cfg_id}{'audit_cfg_ldap_path'  }  ) :
      (  LDAP_Connections_Info( $cfg{$cfg_id}{'audit_cfg_ldap_conn'}) ) ;

    # Need user to be in form user@domain to work between windows/linux with Net::LDAP module
    $ldap_user =
      ( $cfg{$cfg_id}{ 'audit_cfg_ldap_use_conn' }  == 1 ) ?
      ( "$ldap_user\@$ldap_fqdn" ) :
      ( "$ldap_user"             ) ;
  }

  my $win_software = ( $cfg{$cfg_id}{'audit_cfg_win_sft'} ) ? ( 'y' ) : ( 'n' );

  # If a URL is specified at the configuration level, use that
  my $nmap_url = ( $cfg{$cfg_id}{'audit_cfg_nmap_url'} ) ?
    ( $cfg{$cfg_id}{'audit_cfg_nmap_url'} ) :
    ( "$ws_cfg{'base_url'}admin_nmap_input.php" ) ;
  my $lin_url = ( $cfg{$cfg_id}{'audit_cfg_lin_url'} ) ?
    ( $cfg{$cfg_id}{'audit_cfg_lin_url'} ) :
    ( "$ws_cfg{'base_url'}admin_pc_add_2.php" ) ;
  my $win_url = ( $cfg{$cfg_id}{'audit_cfg_win_url'} ) ?
    ( $cfg{$cfg_id}{'audit_cfg_win_url'} ) :
    ( "$ws_cfg{'base_url'}admin_pc_add_2.php" ) ;

  my %options = (
    'audit_user'         => $audit_user,
    'audit_pass'         => $audit_pass,
    'config_id'          => $cfg_id,
    'action'             => $action,
    'com_path'           => $com_path,
    'nmap_args'          => $nmap_args,
    'nmap_path'          => $nmap_path,
    'ldap_user'          => $ldap_user,
    'ldap_pass'          => $ldap_pass,
    'ldap_server'        => $ldap_server,
    'ldap_path'          => $ldap_path,
    'ldap_fqdn'          => $ldap_fqdn,
    'cfg_name'           => $cfg_name,
    'software_windows'   => $win_software,
    'nmap_url'           => $nmap_url,
    'linux_url'          => $lin_url,
    'windows_url'        => $win_url,
    'ldap_page'          => $cfg{$cfg_id}{ 'audit_cfg_ldap_page'        },
    'windows_uuid'       => $cfg{$cfg_id}{ 'audit_cfg_win_uuid'         },
    'os'                 => $cfg{$cfg_id}{ 'audit_cfg_os'               },
    'vbs'                => $cfg{$cfg_id}{ 'audit_cfg_win_vbs'          },
    'max_audits'         => $cfg{$cfg_id}{ 'audit_cfg_max_audits'       },
    'wait_time'          => $cfg{$cfg_id}{ 'audit_cfg_wait_time'        },
    'software_linux'     => $cfg{$cfg_id}{ 'audit_cfg_lin_sft'          },
    'software_list_only' => $cfg{$cfg_id}{ 'audit_cfg_lin_sft_lst'      },
    'software_list'      => $cfg{$cfg_id}{ 'audit_cfg_sft_lst'          },
    'enable_logging'     => $cfg{$cfg_id}{ 'audit_cfg_log_enable'       },
    'command_box'        => $cfg{$cfg_id}{ 'audit_cfg_command_list'     },
    'pc_list'            => $cfg{$cfg_id}{ 'audit_cfg_pc_list'          },
    'command_list'       => $cfg{$cfg_id}{ 'audit_cfg_cmd_list'         },
    'interact'           => $cfg{$cfg_id}{ 'audit_cfg_command_interact' },
    'local_user'         => $cfg{$cfg_id}{ 'audit_cfg_audit_local'      },
    'ip_start'           => $cfg{$cfg_id}{ 'audit_cfg_ip_start'         },
    'ip_end'             => $cfg{$cfg_id}{ 'audit_cfg_ip_end'           },
    'filter'             => $cfg{$cfg_id}{ 'audit_cfg_filter'           },
    'filter_case'        => $cfg{$cfg_id}{ 'audit_cfg_filter_case'      },
    'filter_inverse'     => $cfg{$cfg_id}{ 'audit_cfg_filter_inverse'   },
    'audit_type'         => $cfg{$cfg_id}{ 'audit_cfg_type'             },
  );

  return \%options;
}

sub Get_Schedule_Info {
  my $sched_id = shift;

  my $sql = "SELECT * FROM audit_schedules WHERE audit_schd_id = '$sched_id'";
  my %cfg = %{ MySQL_Execute($sql,"select") };

  my %options = (
    'id'            => $cfg{$sched_id}{'audit_schd_id'         },
    'name'          => $cfg{$sched_id}{'audit_schd_name'       },
    'type'          => $cfg{$sched_id}{'audit_schd_type'       },
    'disable_log'   => $cfg{$sched_id}{'audit_schd_log_disable'},
    'email_log'     => $cfg{$sched_id}{'audit_schd_email_log'  },
  );

  return \%options;
}

sub Get_Audit_Settings {
  my %options;
  my $cfg_sql = "SELECT audit_settings_id, audit_settings_interval,
                        audit_settings_service_name, audit_settings_active,
                        audit_settings_runas_service, audit_settings_script_only,
                        audit_settings_base_url
                 FROM audit_settings LIMIT 1";

  my %cfg  = %{ MySQL_Execute($cfg_sql,"select") };

  foreach my $id ( keys %cfg ) {
    $options{'active'       } = $cfg{$id}{'audit_settings_active'       };
    $options{'runas_service'} = $cfg{$id}{'audit_settings_runas_service'};
    $options{'service_name' } = $cfg{$id}{'audit_settings_service_name' };
    $options{'base_url'     } = $cfg{$id}{'audit_settings_base_url'     };
    $options{'script_only'  } = $cfg{$id}{'audit_settings_script_only'  };
    $options{'poll_interval'} = $cfg{$id}{'audit_settings_interval'     };
  }

  return \%options;
}

############################################################
# Purpose : Get the path to a file in a portable way
# Usage   : Called internally
#             Arg 1 : A filename
# Returns : The path to the file, or bare return on failure

sub Get_File_Path {
  my $file = shift;

  eval {
    require File::Which;
    File::Which->import;
  };

  if ( $@ ) {
    Log_Cron("ERROR: Missing File::Which module");
    return;
  }
  else {
    return which($file);
  }
}

############################################################
# Purpose : Parse config and audit based on options returned
# Usage   : Called with --run-config
#             Arg 1 : config id
# Returns : Nothing to return

sub Audit_Configuration {
  my $config_id = shift;

  if ( defined $DAEMON ) {
    chdir '/'                  or croak "Can't chdir to /: $!";
    open STDIN, '/dev/null'    or croak "Can't read /dev/null: $!";
    open STDOUT, '>>/dev/null' or croak "Can't write to /dev/null: $!";
    open STDERR, '>>/dev/null' or croak "Can't write to /dev/null: $!";

    defined( my $pid = fork ) or croak "Can't fork: $!";

    exit 0 if ( $pid );


    # Daemonized process starts here ...
    setsid  or croak "Can't start a new session: $!";
    umask 0;
  }

  my %cfg = %{ Get_Config_Info($config_id) };
  my @computers = Get_Computer_List($config_id);
  $cfg{'sched_id'} = "none";

  Log_Cron("Running Configuration: $cfg{'cfg_name'}");
  Run_Audits( \@computers, $cfg{'action'}, \%cfg );
  Log_Cron("Finished Running Configuration: $cfg{'cfg_name'}");
}

############################################################
# Purpose : Parse config from the schedule and do the audit
# Usage   : Dispatcher from Schedule::Cron service
#             Arg 1 : schedule id
#             Arg 2 : config id
#             Arg 3 : schedule name
# Returns : Nothing to return

sub Audit_Schedules {
  my ( $sched_id, $config_id, $sched_name ) = @_;

  # The schedule shouldn't run if these are true...
  if ( $DAEMON && ( not Get_Daemon_PID() or $LOOP_PID != Get_Daemon_PID() ) ) {
     Cron_Stop($LOOP_PID) if ( PID_Exists($LOOP_PID) );
     exit 1;
  }

  my %s_cfg = %{ Get_Schedule_Info($sched_id) };
  my %a_cfg = %{ Get_Config_Info($config_id)  };
  $a_cfg{'sched_id'      } = $sched_id;
  $a_cfg{'enable_logging'} = '0' if ( $s_cfg{'disable_log'} == 1 );

  my @computers = Get_Computer_List($config_id);

  # Update the last run timestamp for the schedule
  my $sql  = "UPDATE audit_schedules 
              SET    audit_schd_last_run='" . time . "'
              WHERE  audit_schd_id='$sched_id'";

  MySQL_Execute($sql,"non_select");

  Log_Cron("Running Schedule: $s_cfg{'name'}");
  my $ts = Run_Audits( \@computers, $a_cfg{'action'}, \%a_cfg );
  Log_Cron("Finished Running Schedule: $s_cfg{'name'}");

  Send_Audit_Log_Email($ts,$sched_id) if ( $s_cfg{'email_log'} );
  Update_Next_Run($sched_id);
}

############################################################
# Purpose : Execute command(s) on a remote host.
# Usage   : Called with host and options for audit
#             Arg 1 : hostname/ip address
#             Arg 2 : options hash ref - nmap options, etc
#             Arg 3 : starting timestamp
# Returns : Undef on failure, 1 if success

sub Audit_Command {
  my ( $host, $options, $start ) = @_;

  my ( $exec_time, $login_fail );
  my ( @commands, %cmd_info, $log_message );
  my $count = 0;
  my $p_ts  = time;

  # Get the list of commands, using a count to order them sequentially 
  if ( $$options{'command_list'} ) {
    my @cmd_list = split /,/, $$options{'command_list'};
    foreach my $id ( @cmd_list ) {
      $count++;
      my $sql = "SELECT * FROM audit_commands WHERE audit_cmd_id = '$id'";
      my %cmd = %{ MySQL_Execute($sql,"select") };
      $cmd_info{$count}{'id'     } = $cmd{$id}{'audit_cmd_id'     };
      $cmd_info{$count}{'name'   } = $cmd{$id}{'audit_cmd_name'   };
      $cmd_info{$count}{'command'} = $cmd{$id}{'audit_cmd_command'};
    }
  }
  elsif ( $$options{'command_box'} ) {
    my @cmd_list = split /\n/, $$options{'command_box'};
    foreach my $command ( @cmd_list ) {
      $count++;
      $cmd_info{$count}{'command'} = $command;
    }
  }

  require Net::Ping;
  Net::Ping->import;

  my $p = Net::Ping->new('tcp','10');

  # Basic connectivity check on host
  if ( $p->ping($host) ) {
    # Sort them first to make sure they are executed in the correct order
    foreach my $i ( sort keys %cmd_info ) {
      my $ts  = time;
      ( $cmd_info{$i}{'exit_status'} , $cmd_info{$i}{'output'} ) =
        Run_Command($cmd_info{$i}{'command'},$host,$options);
      my ( $hour, $min, $sec ) = ( gmtime( ( time - $ts ) ) )[2,1,0];
      $log_message = 
        ( $cmd_info{$i}{'exit_status'} ) ?
        ( "Error ($hour hr $min min $sec sec) : Command - '$cmd_info{$i}{'command'}' "   ) :
        ( "Success ($hour hr $min min $sec sec) : Command - '$cmd_info{$i}{'command'}' " ) ;
      Log_Audit(
        $log_message,
        $host,
        $options,
        $start
      );
    }
    my ( $hour, $min, $sec ) = ( gmtime( ( time - $p_ts ) ) )[2,1,0];

    $log_message = "Finished Running Commands in $hour hrs $min min $sec sec";
  }
  else {
    $log_message = "Cannot Connect to Host";
  }

  Log_Audit(
    $log_message,
    $host,
    $options,
    $start
  );
}

############################################################
# Purpose : Execute a command on a remote host.
# Usage   : Called with host and options for audit
#             Arg 1 : command to run
#             Arg 1 : hostname/ip address
#             Arg 2 : options hash ref
# Returns : Output of command, Undef on failure and exit 

sub Run_Command {
  my ( $command, $host , $options  ) = @_;
  my ( $log, $output, $exit_status );

  # Windows, use winexe/remcom.exe
  if ( $$options{'os'} eq 'windows' ) {
    # Make sure any backslashes for UNC paths, etc, are escaped on Linux
    $command =~ s/\\/\\\\/g if ( not $WINDOWS_SERVER );
    my $sys_call = Get_Remote_Command($options,$host) . " \"$command\"";

    # We do care about the exit status of these children...
    local $SIG{CHLD} = 'DEFAULT';

    $CHILD_PID = open my $WINEXE, "$sys_call |";

    # Not sure if it makes sense to log the output or give an option to
    # just email it. So just record success or not for now
    while (<$WINEXE>) {
      $output .= $_;
    }

    close $WINEXE;

    $exit_status = $?>>8;
  }
  # Linux, use Net:SSH::Expect
  elsif ( $$options{'os'} eq 'linux' ) {
  }

  return ( $exit_status, $output );
}

sub Get_Remote_Command {
  my ( $options, $host ) = @_;
  my $command;

  # This is a *little* insecure, perhaps find a way to securely use a smb credentials file
  # or get RemCom.exe and Winexe working with expect

  if ( $WINDOWS_SERVER ) {
    # Need the host before the user so remcom knows to login using the local account
    $$options{'audit_user'} =~ s/(.*)/$host\\$1/ if ( $$options{'local_user'} );
    $command  = "$$options{'com_path'} /user:$$options{'audit_user'} ";
    $command .= "/pwd:$$options{'audit_pass'} \\\\$host";
  }
  else {
    my $interact = ( $$options{'interact'} == 1 ) ? ( '--system --interactive=1' ) : ( '' );
    $command  = "$$options{'com_path'} $interact --uninstall -U ";
    $command .= "$$options{'audit_user'}%'$$options{'audit_pass'}' //$host";
  }

  return $command;
}

############################################################
# Purpose : Perform an nmap audit given the host/options
# Usage   : Called with host and options for audit
#             Arg 1 : hostname/ip address
#             Arg 2 : options hash ref - nmap options, etc
#             Arg 3 : starting timestamp
# Returns : Undef on failure, 1 if success

sub Audit_Nmap {
  my ( $host, $opt, $start  ) = @_;
  my ( $log_message, $out );

  $$opt{'nmap_args'} .= " -O -v $host";

  my $cmd =
    ( $WINDOWS_SERVER ) ?
    ( "\"$$opt{'nmap_path'}\" $$opt{'nmap_args'}"      ) :
    ( "sudo \"$$opt{'nmap_path'}\" $$opt{'nmap_args'}" ) ;

  $CHILD_PID = open my $NMAP, "$cmd |";

  while(<$NMAP>) {
    next if ( /^(Initiating|Scanning|Discovered|Retrying).*/i );
    $log_message = "Nmap needs root priveleges"             if ( m/.*requires root.*/                        );
    $log_message = "User '$1' not setup in Sudo properly"   if ( m/\[sudo\] password for ([-\w]+):.*/i       );
    $log_message = "Cannot find 'Sudo' in your path"        if ( m/sudo: command not found/i                 );
    $log_message = "Unable to Scan Host - No Connectivity"  if ( m/.*(host seems down|failed to resolve).*/i );
    $out .= $_;
  }

  close $NMAP;

  if ( not $log_message and $out ) {
    require LWP::UserAgent;
    LWP::UserAgent->import;

    # Post to the webpage
    my $ua       = LWP::UserAgent->new();
    my $response = $ua->post(
      $$opt{'nmap_url'},
      [
        'add'    => $out,
        'submit' => 'Submit',
      ]
    );

    Log_Audit(
      "Port Scan Completed Successfuly",
      $host,
      $opt,
      $start
    );
    return 1;
  }
  else {
    Log_Audit(
      $log_message,
      $host,
      $opt,
      $start
    );
    return;
  }
}

############################################################
# Purpose : Perform a windows audit given the host/options
# Usage   : Called with host and options for audit
#             Arg 1 : hostname/ip address
#             Arg 2 : options hash ref - user,pass,vbs path,etc
#             Arg 3 : Timestamp of main audit process
# Returns : Undef on failure, 1 if success

sub Audit_Windows {
  my ( $host, $options, $start ) = @_;
  my (
       $exec_time, $log_message,
       $error    , $post_bad   ,
       $post_good
  );

  require Net::Ping;
  Net::Ping->import;

  my $command =
      Get_Remote_Command($options,$host) . ' "cscript \"'.$$options{'vbs'}.'\" '         .
      '/cmd_args_only /non_ie_page:\"'.$$options{'windows_url'}.'\" /verbose:y '         .
      '/software_audit:'.$$options{'software_windows'}.' /strComputer:. /online:yesxml ' .
      '/uuid_type:'.$$options{'windows_uuid'}.'"';

  # Ping TCP port 135 to check for basic WMI connectivity
  my $p = Net::Ping->new('tcp','10');
  $p->port_number('135');

  if ( $p->ping($host) ) {
    $CHILD_PID = open my $WINEXE, "$command |";

    while(<$WINEXE>) {
      $log_message = "Failed to login with given credentials" if ( m/.*NT_STATUS_LOGON_FAILURE.*/i                   );
      $log_message = "Cannot find audit.vbs file"             if ( m/^Input Error: Can not find script file.*/i      );
      $log_message = "Cannot find audit.vbs file"             if ( m/^CScript Error:.*network path was not found.*/i );
      $post_bad    = $1                                       if ( m/^Unable to send XML to server.*?(\d+)/i         );
      $exec_time   = $1                                       if ( m/.*Execution Time:\s(\d+)\s.*/i                  );
      $post_good   = 1                                        if ( m/^XML sent to server.*?(\d+)/i                   );
    }

    close $WINEXE;

    if ( defined $exec_time ) {
      if ( defined $post_good ) {
        $log_message = "Audit Completed Successfuly in $exec_time seconds";
      }
      else {
        $log_message = 
          ( defined $post_bad ) ?
          ( "Audit received HTTP status code $post_bad when submitting results" ) :
          ( "The audit never sent results to the server: $$options{'windows_url'}" ) ; 
      }
    }
    elsif ( not defined $log_message ) {
      $log_message = "Audit Stopped Abnormally";          
      $error       = 1;
    }

    Audit_Nmap($host,$options,$start) if ( $$options{'action'} eq 'pc_nmap' );
  }
  else {
    ( $log_message , $error ) = ( "Cannot Connect to Host" , 1 );
  }

  Log_Audit(
    $log_message,
    $host,
    $options,
    $start
  );

  ( $error ) ? return : return 1;
}

############################################################
# Purpose : Query LDAP to get computer names
# Usage   : Internally called from a sub in Audit_Schedules
#             Arg 1 : Hash of audit config options
# Returns : An array of computer names

sub Get_LDAP_Computers {
  my $cfg = shift;
  my ( @computers, $cookie );

  eval {
    require Net::LDAP; 
    require Net::LDAP::Control::Paged;
    Net::LDAP->import; 
  };

  if ( $@ ) {
    Log_Cron("ERROR: Net::LDAP module not installed");
    exit 1;
  }

  my $ldap_page  = Net::LDAP::Control::Paged->new( 'size' => $$cfg{'ldap_page'} );
  my $ldap_conn  = Net::LDAP->new( $$cfg{'ldap_server'}, 'timeout' => 30 ) 
   or ( Log_Cron("ERROR: LDAP Connection Failure for Config - $$cfg{'cfg_name'}") and exit 1 );
  my $ldap_login = $ldap_conn->bind( $$cfg{'ldap_user'} , password => $$cfg{'ldap_pass'} );

  if ( $ldap_login->is_error ) {
    Log_Cron("ERROR: LDAP Authentication Failure for Config - $$cfg{'cfg_name'}");
    exit 1;
  }

  my @search_args = (
    'base'     => $$cfg{'ldap_path'},
    'scope'    => "sub",
    'filter'   => "(objectClass=computer)",
    'control'  => [ $ldap_page ], 
    'attrs'    => [ 'cn' ], 
  );

  while ( 1 ) {
    my $search = $ldap_conn->search( @search_args );

    $search->code and last;   # success returns zero

    foreach my $entry ( $search->entries ) {
      push @computers, $entry->get_value("CN");
    }

    # End the loop if there are no search items left.
    my ($resp)  = $search->control( Net::LDAP::Constant->LDAP_CONTROL_PAGED ) or last;
    $cookie     = $resp->cookie or last;

    $ldap_page->cookie($cookie);
  }

  # Tell the server there's nothing left for us to grab if search ends badly
  if ( $cookie ) {
    $ldap_page->cookie($cookie);
    $ldap_page->size(0);
    $ldap_conn->search(@search_args);
    Log_Cron("ERROR: LDAP Search Ended Unexpectedly for Config - $$cfg{'cfg_name'}");
  }
 
  return Filter_Computers( \@computers, $cfg );
}

############################################################
# Purpose : Get LDAP info from ldap_connections table
# Usage   : Called from Get_LDAP_Computers for auth info
#             Arg 1 : ldap_connection ID
#             Arg 2 : if defined, formats username with @
# Returns : Info requested from arg 2

sub LDAP_Connections_Info {
  my ( $conn_id, $audit_cred ) = @_;
  my $sql       = 
    "SELECT ldap_connections_id, ldap_connections_nc, ldap_connections_server, ldap_connections_fqdn,
            AES_DECRYPT(ldap_connections_user,'$AES_KEY'    ) AS ldap_connections_user,
            AES_DECRYPT(ldap_connections_password,'$AES_KEY') AS ldap_connections_password
     FROM ldap_connections WHERE ldap_connections_id = '$conn_id'";
  my %ldap_conn = %{ MySQL_Execute($sql,"select") };

  # Winexe needs fqdn with the username.
  my $ldap_user =
    ( defined $audit_cred and $ldap_conn{$conn_id}{'ldap_connections_user'} !~ m/.*@.*/              ) ?
    ( "$ldap_conn{$conn_id}{'ldap_connections_user'}\@$ldap_conn{$conn_id}{'ldap_connections_fqdn'}" ) :
    ( "$ldap_conn{$conn_id}{'ldap_connections_user'}"                                                ) ;

  return (
    $ldap_user                                       , $ldap_conn{$conn_id}{ 'ldap_connections_password' },
    $ldap_conn{$conn_id}{ 'ldap_connections_server' }, $ldap_conn{$conn_id}{ 'ldap_connections_nc'       },
    $ldap_conn{$conn_id}{ 'ldap_connections_fqdn'   }
  )
}

############################################################
# Purpose : Get a list of ips based on the ip range
# Usage   : Internally called from a sub in Audit_Schedules
#             Arg 1 : audit config hash ref
#             Arg 2 : audit config id
# Returns : An array of ip addresses

sub Get_IP_Range_Computers {
  my $cfg = shift;
  my @computers;

  my ( $ip_string , $ip_start ) = $$cfg{'ip_start'} =~ m/(.*\.)(.*)$/; 

  while ( $ip_start <= $$cfg{'ip_end'} ) {
    push @computers, $ip_string . $ip_start;
    $ip_start++;
  }

  return @computers;
}

############################################################
# Purpose : Get a list of PCs based on a MySQL query
# Usage   : Internally called from a sub in Audit_Schedules
#             Arg 1 : audit config id
#             Arg 2 : audit config hash ref
# Returns : An array of hostnames

sub Get_MySQL_Computers {
  my ( $config_id, $cfg ) = @_;
  my ( @computers, @tables, @q_tables, $query );

  my %op = (
    'begins'     => sub { my ( $table, $field, $data ) = @_; return "$table.$field LIKE ". "'$data%'";  },
    'ends'       => sub { my ( $table, $field, $data ) = @_; return "$table.$field LIKE ". "'%$data'";  },
    'equals'     => sub { my ( $table, $field, $data ) = @_; return "$table.$field = ".    "'$data'";   },
    'notequal'   => sub { my ( $table, $field, $data ) = @_; return "$table.$field != ".   "'$data'";   },
    'contains'   => sub { my ( $table, $field, $data ) = @_; return "$table.$field LIKE ". "'%$data%'"; },
    'notcontain' => sub { my ( $table, $field, $data ) = @_; return "$table.$field NOT LIKE '%$data%'"; },
  );

  # Map the uuid on the table specified to the system uuid
  my %uuid_op = (
    'network_card'   => "system_uuid = net_uuid AND" ,
    'scheduled_task' => "system_uuid = sched_task_uuid AND",
    'software'       => "system_uuid = software_uuid AND",
    'motherboard'    => "system_uuid = motherboard_uuid AND",
    'processor'      => "system_uuid = processor_uuid AND",
    'service'        => "system_uuid = service_uuid AND",
    'sound'          => "system_uuid = sound_uuid AND",
    'usb'            => "system_uuid = usb_uuid AND",
    'system'         => "",
    'video'          => "system_uuid = video_uuid AND"
  );

  my $sql    = "SELECT * FROM mysql_queries WHERE mysql_queries_cfg_id = '$config_id'";
  my %q_list = %{ MySQL_Execute($sql,"select") };

  $query = "SELECT system_uuid, system_name FROM system";

  # Get all the tables, make a separate array to include all tables
  foreach my $key ( keys %q_list ) {
    my $table = $q_list{$key}{'mysql_queries_table'};

    push @q_tables, $table if ( not grep /^$table$/, @q_tables );
    push @tables, $table   if ( $table ne 'system' and not grep /^$table$/, @tables );
  }

  $query .= "," . join ',', @tables if ( scalar(@tables) > 0 );
  $query .= " WHERE ";

  # Loop on each table to build the query
  my $count = 0;
  foreach my $table ( @q_tables ) {
    $query .= ( $count == 0 ) ? " ( $uuid_op{$table} " : " AND ( $uuid_op{$table} ";
    my $q_count = 0;
    # Check for any fields to search on this table
    foreach my $key ( keys %q_list ) {
      my $sort  = $q_list{$key}{ 'mysql_queries_sort'  };
      my $field = $q_list{$key}{ 'mysql_queries_field' };
      my $data  = $q_list{$key}{ 'mysql_queries_data'  };
      my $q_tbl = $q_list{$key}{ 'mysql_queries_table' };
      next if ( $q_tbl ne $table ); 
      ( $q_count == 0 ) ?
      ( $query .= " " . $op{$sort}->($table,$field,$data) )   :
      ( $query .= " AND " . $op{$sort}->($table,$field,$data) ) ;
      $q_count++;
    }
    $query .= " )";
    $count++;
  }

  #print "query is : $query\n";
  my %pc_list = %{ MySQL_Execute($query,"select") };

  # Populate the computers array, make sure we don't return duplicates
  foreach my $uuid ( keys %pc_list ) {
    my $pc = $pc_list{$uuid}{'system_name'};
    push @computers, $pc if ( not grep /^$pc$/, @computers );
  }

  return Filter_Computers( \@computers, $cfg );
}

##############################################################
# Purpose : Filter an array of hosts by a regex if specified
# Usage   : Called from subs before returing an array of hosts
#             Arg 1 : Computer array ref
#             Arg 2 : Audit config hash ref
# Returns : An array of filtered hostnames

sub Filter_Computers {
  my ( $computers, $cfg ) = @_;

  if ( $$cfg{'filter'} ) {
    if ( $$cfg{'filter_case'} ) {
      @{ $computers } =
        ( $$cfg{'filter_inverse'}                     ) ?
        ( grep !/$$cfg{'filter'}/i  , @{ $computers } ) :
        ( grep  /$$cfg{'filter'}/i  , @{ $computers } ) ;
    }
    else {
      @{ $computers } =
        ( $$cfg{'filter_inverse'}                   ) ?
        ( grep !/$$cfg{'filter'}/ , @{ $computers } ) :
        ( grep  /$$cfg{'filter'}/ , @{ $computers } ) ;
    }
  }

  return @{ $computers };
}

############################################################
# Purpose : Process a list of computers for auditing
# Usage   : Internally called from Audit_Schedules
#             Arg 1 : Array of computers/ip addresses
#             Arg 2 : Audit action (pc/nmap/etc)
#             Arg 3 : A hash ref with audit options
# Returns : The unique timestamp for the audit log

sub Run_Audits {
  my ( $comp_list , $action, $options ) = @_;
  my ( $max, $wait ) = ( $$options{ 'max_audits' }, $$options{ 'wait_time'  } );
  my %kids;               # Tracked forked process info
  my $timestamp = time;   # Track initial time of main script

  # Schedule::Cron sets its own reaper. Override this to avoid zombies
  local $SIG{ CHLD } = 'IGNORE';


  my %audit = (
    'pc'      => sub { 
                my ( $host ) = @_;
                ( $$options{'os'} eq 'windows'                    ) ?
                ( return Audit_Windows($host,$options,$timestamp) ) :
                ( return Audit_Linux($host,$options,$timestamp)   ) ; 
              },
    'pc_nmap' => sub { 
                my ( $host ) = @_;
                ( $$options{'os'} eq 'windows'                    ) ?
                ( return Audit_Windows($host,$options,$timestamp) ) :
                ( return Audit_Linux($host,$options,$timestamp)   ) ; 
              },
    'nmap'    => sub { 
                my ( $host ) = @_;
                return Audit_Nmap($host,$options,$timestamp);
              },
    'command' => sub { 
                my ( $host ) = @_;
                return Audit_Command($host,$options,$timestamp);
              }
  );

  # Start auditing computers here...
  while ( my $host = pop @{ $comp_list } ) {
    my $pid = fork();

    if ( $pid ) {             # The Parent
      $kids{$pid}{'host' } = $host;
      $kids{$pid}{'start'} = time;
    }
    elsif ( $pid == 0 ) {     # The Child
      ( $audit{$action}->($host) ) ? exit 0 : exit 1 ;
    }
    else {                    # Something went wrong with fork ...
      Log_Audit(
        "Unable to fork audit",
        $host,
        $options,
        $timestamp
      );
      next;
    }

    # Max audits aren't running and this wasn't the last, so next
    next if ( scalar(keys %kids) < $max and scalar( @{ $comp_list } ) > 0 );  
    # Max amount of forks are running, wait for something to complete
    my @delete = wait_on_kids(\%kids, $max, scalar( @{ $comp_list } ), $wait, $options, $timestamp);
    # Remove child processes, that are done, from the hash
    foreach my $d_pid ( @delete ) { delete $kids{$d_pid} }
  }

  # The audit is done, get the elapsed time
  my ( $hour, $min, $sec ) = ( gmtime( ( time - $timestamp ) ) )[2,1,0];

  Log_Audit(
      "Script Execution Time: $hour hr $min min $sec sec",
      '',
      $options,
      $timestamp
  );

  return $timestamp;
}

############################################################
# Purpose : Monitor the status of forked audits
# Usage   : Internally called from Run_Audits
#             Arg 1 : Hash of process info
#             Arg 2 : Max simultaneous audits
#             Arg 3 : Total number of audits left
#             Arg 4 : Wait time before kill
#             Arg 5 : Hash of options for audit config
#             Arg 6 : Main timestamp of audit
# Returns : An array of PIDs no longer active

sub wait_on_kids {
  my ( $kids, $max, $num_left, $wait, $opt, $timestamp ) = @_;
  my @dead_kids;

  while ( 1 ) {
    foreach my $pid ( keys %{ $kids } ) {
      if ( PID_Exists($pid) ) {
        if ( time - $$kids{$pid}{'start'} > $wait ) {  # Has it run too long?
          kill 15, $pid; # Ask nicely the first time
          sleep 1;
          kill 9, $pid if ( PID_Exists($pid) );
          Log_Audit(
            "Killed Hanging Audit, PID $pid",
            $$kids{$pid}{'host'},
            $opt,
            $timestamp
          );
          push @dead_kids, $pid; 
          delete $$kids{$pid};
        }
      }
      else {
        push @dead_kids, $pid; 
        delete $$kids{$pid};
      }
    }
    sleep 1;
    redo if ( scalar(keys %{ $kids } ) != 0 and $num_left == 0 );
    redo if ( scalar(keys %{ $kids } ) == $max );
    last;
  }
  return @dead_kids;
}

############################################################
# Purpose : Get the user/pass for the actual audit
# Usage   : Called from Get_Config_Info
#             Arg 1 : The config id
# Returns : The user and pass unencrypted

sub Get_Config_Auth {
  my $config_id = shift;
  my $sql = "SELECT audit_cfg_id,
               AES_DECRYPT(audit_cfg_audit_user,'$AES_KEY') AS audit_user,
               AES_DECRYPT(audit_cfg_audit_pass,'$AES_KEY') AS audit_pass
             FROM audit_configurations WHERE audit_cfg_id = '$config_id'";
  my %audit_auth = %{ MySQL_Execute($sql,"select") };

  return (
    $audit_auth{$config_id}{ 'audit_user' }, $audit_auth{$config_id}{ 'audit_pass' }
  )
}

############################################
#        Cron Service Helper Subs          #
############################################

############################################################
# Purpose : Run when a sigint/sigterm is caught and shutdown
# Usage   : Called on a sigint/sigterm/service stop
# Returns : 0 on success, 1 on failure

sub Cron_Cleanup {
  my $status = 0;

  # There is a subprocess that may need to be killed
  kill 9, $CHILD_PID if ( defined($CHILD_PID) and PID_Exists($CHILD_PID) );

  # If the loop caught the signal, kill the schedules
  if ( defined($LOOP_PID) and $LOOP_PID == $$ ) {
    if ( $DAEMON ) {
      Write_Daemon_PID('0');
      MySQL_Execute("UPDATE `audit_settings` SET audit_settings_active='0'","non_select");
    }
    Log_Cron("Caught SIGINT/SIGTERM, Shutting Down");
    foreach my $id ( keys %SCHEDULE_PIDS ) {
      kill 9, $SCHEDULE_PIDS{$id}{'pid'} or
      ( Log_Cron("Cannot Stop Schedule: $SCHEDULE_PIDS{$id}{'name'}") and $status = 1 );
    }
    Log_Cron("Stopped All Web-Schedules") if ( not $status );
  }

  exit $status;
}

############################################################
# Purpose : Parse a schedule into cron format
# Usage   : Internally by sub name
#             Arg 1 : schedule id
# Returns : The formatted cron line

sub Get_Cron_Line {
  my ( $id ) = @_;
  my $sql = "SELECT * FROM audit_schedules WHERE audit_schd_id = '$id'";
  my %schedule = %{ MySQL_Execute($sql,"select") };

  my $type = $schedule{$id}{'audit_schd_type'};

  my $min    = $schedule{$id}{'audit_schd_strt_min'  };
  my $hr     = $schedule{$id}{'audit_schd_strt_hr'   };
  my $days   = $schedule{$id}{'audit_schd_wk_days'   };
  my $months = $schedule{$id}{'audit_schd_mth_months'};

  my $hr_freq     = $schedule{$id}{'audit_schd_hr_frq_hr'  };
  my $hr_start    = $schedule{$id}{'audit_schd_hr_strt_hr' };
  my $hr_end      = $schedule{$id}{'audit_schd_hr_end_hr'  };
  my $dly_freq    = $schedule{$id}{'audit_schd_dly_frq'    };
  my $between     = $schedule{$id}{'audit_schd_hr_between' };
  my $min_freq    = $schedule{$id}{'audit_schd_hr_frq_min' };
  my $min_start   = $schedule{$id}{'audit_schd_hr_strt_min'};
  my $monthly_day = $schedule{$id}{'audit_schd_mth_day'    };
  my $cron_entry  = $schedule{$id}{'audit_schd_cron_line'  };

  # Some exceptions...
  $min_start  = $schedule{$id}{'audit_schd_hr_strt_min'} if ( $type eq 'hourly' and $between     );
  $min_start  = $schedule{$id}{'audit_schd_hr_frq_min'}  if ( $type eq 'hourly' and not $between );
  my $hours   = ( $between ) ? ( "$hr_start-$hr_end" ) : ( "*" );

  if ( $type eq 'weekly'  ) { return "$min $hr * * $days"               };
  if ( $type eq 'hourly'  ) { return "$min_start $hours/$hr_freq * * *" };
  if ( $type eq 'daily'   ) { return "$min $hr */$dly_freq * *"         };
  if ( $type eq 'monthly' ) { return "$min $hr $monthly_day $months *"  };
  if ( $type eq 'crontab' ) { return "$cron_entry"                      };
}

############################################################
# Purpose : Log events by the cron service in the OA db
# Usage   : Internally by sub name
#             Arg 1 : text/string to log
# Returns : Only warn on failure. 

sub Log_Cron {
  my $log_line = shift;
  $log_line = MySQL_Quote_String($log_line);
  my $sql = "INSERT INTO ws_log ( ws_log_message , ws_log_timestamp,
                                    ws_log_pid        )
                         VALUES ( $log_line        , '" . time . "'    ,
                                    $$                )";

  print "(CRON) " . $log_line . "\n";
  MySQL_Execute($sql,"non_select") or carp "Warning: Cannot Log to DB";
}

############################################################
# Purpose : Log events from audits in the OA db
# Usage   : Internally by sub name
#             Arg 1 : text/string to log
#             Arg 2 : hostname
#             Arg 3 : config hash ref
#             Arg 4 : timestamp
# Returns : Only warn on failure. 

sub Log_Audit {
  my ( $log_line, $host, $cfg, $timestamp ) = @_;
  $log_line = MySQL_Quote_String($log_line);
  $host     = MySQL_Quote_String($host);

  my $sql = "INSERT INTO audit_log ( audit_log_message     , audit_log_host,
                                     audit_log_schedule_id , audit_log_config_id,
                                     audit_log_timestamp   , audit_log_pid      ,
                                     audit_log_time                                )
                            VALUES ( $log_line             , $host               ,
                                     '$$cfg{'sched_id'}'   , '$$cfg{'config_id'}',
                                     '$timestamp'          , '$$'                ,                
                                     " . time . "                                  )";

  my $msg = ( $host ) ? ( "(AUDIT)($host) $log_line\n" ) : ( "(AUDIT) $log_line\n" ) ;
  print $msg;

  if ( $$cfg{'enable_logging'} ) {
    MySQL_Execute($sql,"non_select") or carp "Warning: Cannot Log to DB";
  }
}

############################################
#              Perl Cron Loop              #
############################################

############################################################
# Purpose : Start cron service to monitor schedules and cron
#           service tables 
# Usage   : Using switch --cron-start
# Return  : Exits with a status of 1 on failure

sub Cron_Start {
  eval {
    require Schedule::Cron;
    Schedule::Cron->import;
  };

  if ( $@ ) {
    Log_Cron("ERROR: Missing Schedule::Cron module");
    exit 1;
  }

  if ( defined $DAEMON and not $WINDOWS_SERVER ) {
    chdir '/'                  or croak "Can't chdir to /: $!";
    open STDIN, '/dev/null'    or croak "Can't read /dev/null: $!";
    open STDOUT, '>>/dev/null' or croak "Can't write to /dev/null: $!";
    open STDERR, '>>/dev/null' or croak "Can't write to /dev/null: $!";

    defined( my $pid = fork ) or croak "Can't fork: $!";
    if ( $pid ) {
      exit;
    }

    setsid  or croak "Can't start a new session: $!";
    umask 0;
  }

  # If daemon is specified, do PID check/update
  Start_Daemon_Check() if ( defined $DAEMON );

  my %cron_cfg  = %{ Get_Audit_Settings() };

  # Set the PID to track this process for clean-up
  $LOOP_PID = $$; 

  # Start the main loop to add/remove/stop/start schedules
  while ( 1 ) {
    last if ( $DAEMON and not Daemon_is_Active() );
    Poll_Database();
    sleep $cron_cfg{'poll_interval'};
  }

  Cron_Cleanup();
}

############################################################
# Purpose : Stop cron service if it's running
# Usage   : Using switch --cron-stop, or from sub Audit_Schedule()
#             Arg 1 : The PID of the service (optional)
# Return  : Exit with status of 1 if it cannot stop the PID

sub Cron_Stop {
  my $srv_pid = shift;
  my $pid = ( $srv_pid ) ? ( $srv_pid ) : ( Get_Daemon_PID() );

  if ( not defined $pid ) {
    exit 0;
  }
  else {
    kill 15, $pid;
    sleep 1;
    kill 9, $pid if ( PID_Exists($pid) );
    ( PID_Exists($pid) ) ? ( exit 1 ) : ( exit 0 );
  }
}

############################################################
# Purpose : Check if daemon has been marked to stop
# Usage   : Called in busy-wait loop in Cron_Start
# Return  : audit_settings_active value

sub Daemon_is_Active {
  my %h_cron = %{ MySQL_Execute("SELECT audit_settings_id, audit_settings_active FROM audit_settings LIMIT 1","select") };
  my $active = $h_cron{'1'}{'audit_settings_active'};

  return $active;
}

############################################################
# Purpose : Check the OA DB for daemon PID, see if it exists
# Usage   : Called when starting in daemon mode
# Return  : Bare return if not running, return PID otherwise

sub Get_Daemon_PID {
  my %h_pid = %{ MySQL_Execute("SELECT audit_settings_id, audit_settings_pid FROM audit_settings LIMIT 1","select") };
  my $pid   = $h_pid{'1'}{'audit_settings_pid'};

  return undef if ( $pid == 0 );
  ( PID_Exists($pid) ) ? ( return $pid ) : ( return undef );
}

############################################################
# Purpose : Write the daemon PID to the DB
# Usage   : Called when starting in daemon mode, only arg is
#           a PID
# Return  : Nothing to return

sub Write_Daemon_PID {
  my $pid = shift;

  MySQL_Execute("UPDATE `audit_settings` SET audit_settings_pid='$pid'","non_select");
}

############################################################
# Purpose : Determine if the service can start or not
# Usage   : Called when starting in daemon mode
# Return  : Exit with status of 1 if daemon is running yet

sub Start_Daemon_Check {
  my $pid = Get_Daemon_PID();
  if ( not defined $pid ) {
    Write_Daemon_PID($$);
    MySQL_Execute("UPDATE `audit_settings` SET audit_settings_active='1'","non_select");
    Log_Cron("Started the Web-Schedule Service");
  }
  else {
    Log_Cron("The Web-Schedule Service is Already Running with PID $pid");
    exit 1;
  }
}

############################################################
# Purpose : Check the OA DB for schedule changes
# Usage   : Called from Cron_Start and Win32_Service
# Return  : Nothing to return

sub Poll_Database {
  my $sql = "SELECT audit_schd_id    , audit_schd_cfg_id, audit_schd_name,
                    audit_schd_active, audit_schd_updated 
             FROM audit_schedules";
  my %schedules = %{ MySQL_Execute($sql,"select") };

  # Check all the schedules in the db, decide what to do
  foreach my $id ( sort keys %schedules ) {
    my $active  = grep /^$id$/, sort keys %SCHEDULE_PIDS;
    my $enabled = $schedules{$id}{'audit_schd_active' };
    my $update  = $schedules{$id}{'audit_schd_updated'};
    my $cfg_id  = $schedules{$id}{'audit_schd_cfg_id' };
    my $name    = $schedules{$id}{'audit_schd_name'   };

    # Check if the schedule is ok if it's active
    Check_Audit_Schedule($cfg_id,$id,$name) if ( $active and $enabled );

    # Does it need an update?
    if ( $active and $enabled and $update ) {
      Stop_Audit_Schedule($id,$name);
      Start_Audit_Schedule($cfg_id,$id,$name);
      Log_Cron("Schedule Updated: $name");
    }

    # Was it disabled?
    Stop_Audit_Schedule($id,$name) if ( $active and not $enabled );

    # The schedule isn't running yet but should be
    Start_Audit_Schedule($cfg_id,$id,$name) if ( $enabled and not $active );
  }

  # Check for schedules deleted from the db
  foreach my $id ( sort keys %SCHEDULE_PIDS ) {
    Stop_Audit_Schedule($id,$SCHEDULE_PIDS{$id}{'name'}) if ( not grep /^$id$/, sort keys %schedules );
  }
}
 
############################################################
# Purpose : Stop an audit schedule that's running
# Usage   : Called from the loop in the Cron_Start sub
#             Arg 1 : Schedule ID
#             Arg 2 : Schedule Name
# Return  : Returns 1 on success, undef on failure

sub Stop_Audit_Schedule {
  my ( $schd_id, $name ) = @_;
  if ( ( kill 9, $SCHEDULE_PIDS{$schd_id}{'pid'} ) == '1' ) {
    Log_Cron("Removing Schedule: $name");
    delete $SCHEDULE_PIDS{$schd_id};
    return 1;
  }
  else {
    Log_Cron("ERROR: Cannot Stop Schedule: $name");
    return;
  }
}

############################################################
# Purpose : Start an audit schedule
# Usage   : Called from the loop in the Cron_Start sub
#             Arg 1 : Schedule ID
#             Arg 2 : Schedule Name
#             Arg 3 : hash of schedule db entry
# Return  : Returns 1 on success

sub Start_Audit_Schedule {
  my ( $cfg_id, $schd_id, $name ) = @_;
  MySQL_Execute("UPDATE `audit_schedules` SET audit_schd_updated='0' WHERE audit_schd_id=$schd_id","non_select");
  Update_Next_Run($schd_id);
  Log_Cron("Adding Schedule: $name");
  $SCHEDULE_PIDS{$schd_id}{'pid' } = ${ \&Cron_Fork($cfg_id,$schd_id,$name) };
  $SCHEDULE_PIDS{$schd_id}{'name'} = $name;
  return 1;
}

############################################################
# Purpose : Check to make sure the schedule is running
# Usage   : Called from the loop in the Cron_Start sub
#             Arg 1 : Schedule ID
#             Arg 2 : Schedule Name
#             Arg 3 : hash of schedule db entry
# Return  : Nothing to return

sub Check_Audit_Schedule {
  my ( $cfg_id, $schd_id, $name ) = @_;
  my $pid = $SCHEDULE_PIDS{$schd_id}{'pid'};

  if ( not PID_Exists($pid) ) {
    delete $SCHEDULE_PIDS{$schd_id};
    Start_Audit_Schedule($cfg_id,$schd_id,$name);
    Log_Cron("Re-added Missing Schedule : $name");
  }
}

############################################################
# Purpose : Check if a PID is active/exists on linux/windows
# Usage   : Called in various process management areas
#             Arg 1 : PID to check
# Return  : Returns 1 if it exists, undef if it doesn't

sub PID_Exists {
  my $pid = shift;

  if ( $WINDOWS_SERVER ) {
    my $status = waitpid($pid,&WNOHANG);
    ( $status == 0 ) ? ( return 1 ) : ( return undef ) ; 
  }
  else {
    ( kill 0, $pid ) ? ( return 1 ) : ( return undef ) ;
  }
}

############################################################
# Purpose  : Update the time that the cron entry will run
# Usage    : Internally by sub name.
#             Arg 1 : schedule id
# Returns  : Nothing to return

sub Update_Next_Run {
  my $sched_id = shift;

  my $sql  = "UPDATE audit_schedules 
              SET    audit_schd_next_run='" . Get_Next_Run( Get_Cron_Line($sched_id) ) . "'
              WHERE  audit_schd_id='$sched_id'";

  MySQL_Execute($sql,"non_select");
}

############################################################
# Purpose  : Given a cron entry, determine when it will run
# Usage    : Via an ajax call from audit_schedule.php
#             Arg 1 : Cron line to check
# Returns  : Next run time as epoch

sub Get_Next_Run {
  my $cron_line = shift;

  require Schedule::Cron;
  Schedule::Cron->import;

  return Schedule::Cron->get_next_execution_time($cron_line);
}

############################################################
# Purpose  : Fork a single schedule from the schedule table.
# Usage    : Internally by sub name.
#             Arg 1 : config id
#             Arg 2 : schedule id
#             Arg 3 : schedule name
# Returns  : PID of forked schedule
# Comments : Each schedule is forked so over lapping 
#            schedules don't tie each other up

sub Cron_Fork {
  my ( $cfg_id, $schd_id, $name ) = @_;
  my $cron_line = Get_Cron_Line($schd_id);
  my $cron = new Schedule::Cron(\&Audit_Schedules, 'processprefix' => "OA Schedule '$name'" );

  # Need to set this here too otherwise deactivated schedules turn into zombies :)
  local $SIG{ CHLD } = 'IGNORE';

  $cron->add_entry(
    $cron_line,
    $schd_id,
    $cfg_id,
    $name,
  );

  # Fork the schedule and return the PID
  return $cron->run( 'detach' => 1 );
}

############################################
#                 Main Code                #
############################################

sub Interactive {
  my (
    $audit_type , $audit_sql , $query_test, $nmap_test,
    $start      , $run_config, @audit_list, $config_id,
    $schedule_id, $custom    , $cron_line , $pid_check,
    $stop       , $service_install
  );

  GetOptions(
    'audit-list:s{1,}' => \@audit_list,
    'audit-type=s'     => \$audit_type,
    'audit-sql=s'      => \$audit_sql,
    'config-id=s'      => \$config_id,
    'schedule-id=s'    => \$schedule_id,
    'test-cron=s'      => \$cron_line,
    'test-query=s'     => \$query_test,
    'test-nmap=s'      => \$nmap_test,
    'run-config=s'     => \$run_config,
    'url-path=s'       => \$URL,
    'check-pid'        => sub { $pid_check = '1';  },
    'daemon'           => sub { $DAEMON    = '1';  },
    'cron-start'       => sub { $start     = '1';  },
    'cron-stop'        => sub { $stop      = '1';  },
    'service-custom'   => sub { $custom    = '1';  },
    'help|?'           => sub { Usage()            },
    'version'          => sub { print "$VERSION\n" },
  );

  Cron_Stop()  if ( defined $stop  );
  Cron_Start() if ( defined $start );

  Audit_Configuration($run_config)         if ( $run_config );
  Audit_Schedules($schedule_id,$config_id) if ( $schedule_id and $config_id );

  Test_Query($query_test) if ( $query_test );
  Test_Nmap($nmap_test)   if ( $nmap_test  );

  Service_Install($custom) if ( $SERVICE_INSTALL );

  print Get_Next_Run($cron_line) if ( $cron_line );

  if ( $pid_check ) {
    my $pid = Get_Daemon_PID();
    ( defined $pid ) ? ( exit 0 ) : ( exit 1 ) ;
  }
}

Interactive();
