<?php
// Script to rebuild RuleBasedChatbotService.php

$content = file_get_contents('https://gist.githubusercontent.com/placeholder/rebuild.txt');
file_put_contents('app/Services/RuleBasedChatbotService.php', $content);

echo "File rebuilt successfully!\n";
