<?php
chdir(dirname(__FILE__));
require "connection.php";
require "../../config/raidfix.php";
require "../../config/webhooks/DiscordWebhook.php";
//class
class RateLimit {
    
    /**
     * check rate limit
     *to use:
     *$custom: 0 = predefined (accounts/comments), 1 = custom
     *$type: 1 = accounts, 2 = comments, or table name if custom=1
     *$limit: limit per hour (only used if custom=1, otherwise configured in raidfix.php)
     *$timeColumn: column name for timestamp (default: 'uploadDate', only used if custom=1)
     *returns "-1" if limit exceeded, "1" if allowed
     */
    public function checkLimit($custom, $type, $limit = null, $timeColumn = 'uploadDate') {
        global $db, $discordWebhook, $accperhr, $commentperhr;
        $timeframe = time() - 3600;
        
        if ($custom == 0) {
            if ($type == 1) {
                $query = $db->prepare("SELECT COUNT(*) FROM accounts WHERE registerDate > :time");
                $query->execute([':time' => $timeframe]);
                $count = $query->fetchColumn();
                if ($count >= $accperhr) {
                    if (!empty($discordWebhook)) {
                        $webhook = new DiscordWebhook($discordWebhook);
                        $webhook->setContent("**suspicious activity logged**
account rate limit exceeded
$count/$accperhr accounts in the past hour");
                        $webhook->send();
                    }
                    return "-1";
                }
            } elseif ($type == 2) {
                $query = $db->prepare("SELECT COUNT(*) FROM comments WHERE timestamp > :time");
                $query->execute([':time' => $timeframe]);
                $count = $query->fetchColumn();
                if ($count >= $commentperhr) {
                    if (!empty($discordWebhook)) {
                        $webhook = new DiscordWebhook($discordWebhook);
                        $webhook->setContent("**suspicious activity logged**
comment rate limit exceeded
$count/$commentperhr comments in the past hour");
                        $webhook->send();
                    }
                    return "-1";
                }
            }
        } else {
            if ($limit === null) {
                return "-1";
            }
            $query = $db->prepare("SELECT COUNT(*) FROM $type WHERE $timeColumn > :time");
            $query->execute([':time' => $timeframe]);
            $count = $query->fetchColumn();
            if ($count >= $limit) {
                if (!empty($discordWebhook)) {
                    $webhook = new DiscordWebhook($discordWebhook);
                    $webhook->setContent("**suspicious activity logged**
rate limit exceeded on $type
$count/$limit in the past hour");
                    $webhook->send();
                }
                return "-1";
            }
        }
        
        return "1";
    }
}
?>
