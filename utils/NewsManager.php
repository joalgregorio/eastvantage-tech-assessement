<?php

class NewsManager
{
	private static $instance = null;
	private $commentManager;
	private $db;
	private $table = "news";
	public function __construct(CommentManager $commentManager, DB $db)
	{
		require_once(ROOT . '/class/News.php');
		$this->commentManager = $commentManager;
		$this->db = $db;
	}

	/**
	* list all news
	*/
	public function list()
	{

		$columns = ["*"];
		$rows = $this->db->select($this->table,$columns);
		$news = [];
		foreach($rows as $row) {
			$n = new News();
			$news[] = $n->setId($row['id'])
			  			->setTitle($row['title'])
			  			->setBody($row['body'])
			  			->setCreatedAt($row['created_at']);
		}

		return $news;
	}

	/**
	* add a record in news table
	*/
	public function add($title, $body)
	{
		$newRow = [
			'title'	=> $title,
			'body'	=> $body
		];

		return $this->db->insert($this->table, $newRow);
	}

	/**
	 * list all comments for a specific news entry
	 */
	public function listComments($newsId) 
	{
		return $this->commentManager->list(newsId: $newsId);
	}

	/**
	 * add new comment for a specific news entry
	 */
	public function addComment($newsId, $body) 
	{
		return $this->commentManager->add($newsId, $body);
	}

	/**
	* deletes a news, and also linked comments
	*/
	public function delete($id)
	{
		$this->commentManager->deleteByNewsId($id);
		$filters = [
			'id'	=> $id
		];

		return $this->db->delete($this->table, $filters);
	}
}