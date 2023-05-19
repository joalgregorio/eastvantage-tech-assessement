<?php

define('ROOT', __DIR__);
require_once(ROOT . '/utils/NewsManager.php');
require_once(ROOT . '/utils/CommentManager.php');
require_once(ROOT . '/utils/DB.php');

$db = DB::getInstance();
$commentManager = new CommentManager($db);
$newsManager = new NewsManager($commentManager, $db);

foreach ($newsManager->list() as $news) {
	echo("############ NEWS " . $news->getTitle() . " ############\n");
	echo($news->getBody() . "\n");
	foreach ($newsManager->listComments($news->getId()) as $comment) {
			echo("Comment " . $comment->getId() . " : " . $comment->getBody() . "\n");
	}
}