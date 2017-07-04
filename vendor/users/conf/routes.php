<?php

    return(
		array(
			array(
				'route' => '/users/add/' , 
				'class' => 'UsersAction' , 
				'callback' => 'add_user' , 
				'method' => 'GET'
			) , 
			array(
				'route' => '/users/add/' , 
				'class' => 'UsersAction' , 
				'callback' => 'add_user' , 
				'method' => 'POST'
			) , 
			array(
				'route' => '/users/edit/[i:user_id]/' , 
				'class' => 'UsersAction' , 
				'callback' => 'edit_user' , 
				'method' => 'GET'
			) , 
			array(
				'route' => '/users/edit/[i:user_id]/' , 
				'class' => 'UsersAction' , 
				'callback' => 'edit_user' , 
				'method' => 'POST'
			) , 
			array(
				'route' => '/users/delete/[i:user_id]/' , 
				'class' => 'UsersAction' , 
				'callback' => 'delete_user' , 
				'method' => 'GET'
			) , 
			array(
				'route' => '/users/' , 
				'class' => 'UsersAction' , 
				'callback' => 'grid' , 
				'method' => 'GET'
			)
		)
	);

?>