<?php
chdir(dirname(__FILE__));
require "connection.php";
require "../../config/raidfix.php";
require "../../config/webhooks/DiscordWebhook.php";
//class

class RateLimit {
    // ignore ts, its just to parse stuff
    function parseCustomMessage($message, $type, $count) {
        global $accperhr, $commentperhr, $limit;
        
        $max = 0;

        if ($type == 1) {
            $max = $accperhr;
        } elseif ($type == 2) {
            $max = $commentperhr;
        } else {
            $max = $limit ?? 0;
        }

        if (str_contains($message, '{count}')) {
            $message = str_replace("{count}", $count, $message);
        }
        
        if (str_contains($message, '{max}')) {
            $message = str_replace("{max}", $max, $message);
        }

        return $message;
    }

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
        global $db, $discordWebhook, $accperhr, $commentperhr, $webhookMessageAccount, $webhookMessageAccountPing, $webhookMessageComment, $webhookMessageCommentPing;
        $timeframe = time() - 3600;
        
        if ($custom == 0) {
            if ($type == 1) {
                $query = $db->prepare("SELECT COUNT(*) FROM accounts WHERE registerDate > :time");
                $query->execute([':time' => $timeframe]);
                $count = $query->fetchColumn();
                if ($count >= $accperhr) {
                    if (!empty($discordWebhook)) {
                        $message = !empty($webhookMessageAccount) ? $this->parseCustomMessage($webhookMessageAccount, $type, $count) : "**suspicious activity logged**\naccount rate limit exceeded\n$count/$accperhr accounts in the past hour";

                        if ($webhookMessageAccountPing) {
                            $message = $webhookMessageAccountPing . "\n" . $message;
                        }

                        $webhook = new DiscordWebhook($discordWebhook);
                        $webhook->setContent($message);
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
                        $message = !empty($webhookMessageComment) ? $this->parseCustomMessage($webhookMessageComment, $type, $count) : "**suspicious activity logged**\ncomment rate limit exceeded\n$count/$commentperhr comments in the past hour";

                        if ($webhookMessageCommentPing) {
                            $message = $webhookMessageCommentPing . "\n" . $message;
                        }

                        $webhook = new DiscordWebhook($discordWebhook);
                        $webhook->setContent($message);
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
                    $webhook->setContent("**suspicious activity logged**\nrate limit exceeded on $type\n$count/$limit in the past hour");
                    $webhook->send();
                }
                return "-1";
            }
        }
        
        return "1";
    }
}
?>
