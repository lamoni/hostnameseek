<?php namespace Lamoni\HostnameSeek;

class HostnameSeek
{

    protected $hostsFile;

    protected $hosts = array();

    public function __construct($hostsFile = '/etc/hosts', array $matches=array(), array $blacklist=array())
    {

        /**
         * Save hosts file location
         */

        $this->hostsFile = $hostsFile;

        /*
         * Load hosts file content
         */

        $tempHosts = array_filter(
            explode("\n",
                strtolower(
                    file_get_contents($hostsFile)
                )
            )
        );

        $cleansedHosts = array();

        foreach ($tempHosts as $host) {

            $host = trim($host);

            if (strpos($host, "#") !== 0) { // This should NOT be "false", it needs to begin with # to be excluded

                /*
                 * Remove all tabs
                 */
                while (strpos($host, "\t") !== false) {

                    $host = str_replace("\t", " ", $host);

                }

                /*
                 * Remove all double spaces
                 */
                while (strpos($host, "  ") !== false) {

                    $host = str_replace("  ", " ", $host);

                }

                $host = explode(" ", $host, 2);

                $tempCleansedHosts = array();

                $tempCleansedHosts[$host[0]] = explode(" ", $host[1]);

                foreach ($tempCleansedHosts[$host[0]] as $tempHost) {

                    if (strpos($tempHost, "#") === false && strpos($tempHost, "localhost") === false) {

                        $cleansedHosts[$tempHost] = $host[0];

                    }
                    else {

                        break;

                    }

                }

            }

        }


        /*
         * Find matching, if any
         */

        $matches = array_filter($matches);

        if (count($matches) !== 0) {

            $tempCleansedHosts = array();

            foreach ($cleansedHosts as $hostname=>$alias) {

                foreach ($matches as $match) {

                    /*
                     * Wildcard indicator... be lenient (e.g. 'lab1' will match 'lab1' and 'lab10'
                     */

                    if (preg_match("/$match/i", $hostname) === 1) {

                        $tempCleansedHosts[$hostname] = $alias;

                    }

                }

            }

            $cleansedHosts = $tempCleansedHosts;

        }

        /*
         * Remove blacklist items, if any
         */

        $blacklist = array_filter($blacklist);

        if (count($blacklist) !== 0) {

            $tempCleansedHosts = array();

            foreach ($cleansedHosts as $hostname=>$alias) {

                foreach ($blacklist as $match) {

                    /*
                     * Wildcard indicator... be lenient (e.g. 'lab1' will match 'lab1' and 'lab10'
                     */

                    if (preg_match("/$match/i", $hostname) !== 1) {

                        $tempCleansedHosts[$hostname] = $alias;

                    }

                }

            }

            $cleansedHosts = $tempCleansedHosts;

        }


        $this->hosts = $cleansedHosts;

    }

    public function GetHosts()
    {

        return $this->hosts;

    }

    public function GetIPByHostname($name)
    {

        $name = strtolower($name);

        if (!isset($this->hosts[$name])) {

            return false;

        }

        return $this->hosts[$name];

    }

    public function DoesHostnameExist($name)
    {
        
        if (!$this->GetIPByHostname($name)) {

            return false;

        }

        return true;

    }

    public function GetHostnamesMatching(array $matches)
    {

        $matchedHosts = array();

        foreach ($this->hosts as $hostname=>$ip) {

            foreach ($matches as $match) {

                if (preg_match("/.*$match.*/i", $hostname) === 1) {

                    $matchedHosts[] = array($hostname => $ip);

                }

            }

        }

        return $matchedHosts;
    }

}