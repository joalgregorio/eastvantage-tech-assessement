<?php

class CommentManager
{
	private static $instance = null;
	private $db;
	private $table = 'comments';
	public function __construct(DB $db)
	{
		require_once(ROOT . '/class/Comment.php');
		$this->db = $db;
	}

	/**
	 * list all comments or specific to a news entry
	 */
	public function list($newsId=null)
	{
		$columns = ["*"];
		$filter = [];

		if($newsId) {
			$filters = [ 
				'news_id' => $newsId 
			];
		}

		$rows = $this->db->select($this->table, $columns, $filters);

		$comments = [];
		foreach($rows as $row) {
			$n = new Comment();
			$comments[] = $n->setId($row['id'])
			  ->setBody($row['body'])
			  ->setCreatedAt($row['created_at'])
			  ->setNewsId($row['news_id']);
		}

		return $comments;
	}

	public function add($newsId, $body)
	{
		$newRow = [
			'news_id' 	=> $newsId,
			'body'		=> $body
		];

		return $this->db->insert($this->table, $newRow);
	}

	public function delete($id)
	{
		$filters = [
			'id'	=> $id
		];

		return $this->db->delete($this->table, $filters);

	}

	public function deleteByNewsId($newsId)
	{

		$filters = [
			'news_id'	=> $newsId
		];

		return $this->db->delete($this->table, $filters);
	}
}