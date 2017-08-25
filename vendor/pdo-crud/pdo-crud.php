<?php

    /**
    *   Class provides simple CRUD operations.
    */
    class           PdoCrud
    {
        /**
        *   PDO object.
        */
        var             $PDO = false;

        /**
        *   Method connects to the database.
        */
        function            connect( $ConnnectionData )
        {
            // no need to test this single string. assume that PDO developers did it
            $this->PDO = new PDO( 
                $ConnnectionData[ 'dsn' ] , $ConnnectionData[ 'user' ] , $ConnnectionData[ 'password' ]
            );

			$this->PDO->query( 'SET NAMES utf8' );
        }

        /**
        *   Method builds select query.
        */
        protected function  select_query( $Fields , $TableNames , $Where , $From , $Limit )
        {
            $Query = 'SELECT '.$Fields.' FROM '.$TableNames.' WHERE '.
                $Where.' LIMIT '.intval( $From ).' , '.intval( $Limit );

            return( $Query );
        }

		/**
		*	Method handles request errors.
		*/
		protected function	process_query_error( $Result , $Query )
		{
			if( $Result === false )
			{
				$ErrorInfo = $this->PDO->errorInfo();

				throw( new Exception( $ErrorInfo[ 2 ].' in statement '.$Query ) );
			}
		}

        /**
        *   Getting records.
        */
        function            select( $Fields , $TableNames , $Where = '1 = 1' , $From = 0 , $Limit = 1000000 )
        {
			$Query = $this->select_query( $Fields , $TableNames , $Where , $From , $Limit );

			$Result = $this->PDO->query( $Query );

			$this->process_query_error( $Result , $Query );

            return( $Result->fetchAll( PDO::FETCH_ASSOC ) );
        }

		/**
		*	Method compiles set-query.
		*/
		protected function	set_query( $Record )
		{
			$SetFieldsStatement = [];

			foreach( $Record as $Field => $Value )
			{
				if( is_string( $Value ) )
				{
					$SetFieldsStatement [] = $Field.' = "'.$Value.'"';
				}
				else
				{
					$SetFieldsStatement [] = $Field.' = '.$Value;
				}
			}

			return( implode( ' , ' , $SetFieldsStatement ) );
		}

		/**
        *   Method builds update query.
        */
        protected function  update_query( $TableName , $Record , $Where , $Limit )
        {
            $Query = 'UPDATE '.$TableName.' SET '.$this->set_query( $Record ).
				' WHERE '.$Where.' LIMIT '.$Limit;

            return( $Query );
        }

		/**
		*	Updating records.
		*/
		function			update( $TableName , $Record , $Where , $Limit = 10000000 )
		{
			$Query = $this->update_query( $TableName , $Record , $Where , $Limit );

			$Result = $this->PDO->query( $Query );

			$this->process_query_error( $Result , $Query );

			return( $Result->rowCount() );
		}

		/**
        *   Method builds delete query.
        */
        protected function  delete_query( $TableName , $Where , $Limit )
        {
            $Query = 'DELETE FROM '.$TableName.' WHERE '.$Where.' LIMIT '.intval( $Limit );

            return( $Query );
        }

		/**
		*	Deleting records.
		*/
		function			delete( $TableName , $Where , $Limit = 10000000 )
		{
			$Query = $this->delete_query( $TableName , $Where , $Limit );

			$Result = $this->PDO->query( $Query );

			$this->process_query_error( $Result , $Query );

			return( $Result->rowCount() );
		}

		/**
		*	Method compiles lock queries.
		*/
		protected function	lock_query( $Tables , $Modes )
		{
			$Query = [];

			foreach( $Tables as $i => $Table )
			{
				$Query [] = $Table.' '.$Modes[ $i ];
			}

			$Query = 'LOCK TABLES '.implode( ' , ' , $Query );

			return( $Query );
		}

		/**
		*	Method locks tables
		*/
		function			lock( $Tables , $Modes )
		{
			$Query = $this->lock_query( $Tables , $Modes );

			$Result = $this->PDO->query( $Query );

			$this->process_query_error( $Result , $Query );
		}

		/**
		*	Method unlocks locked tables.
		*/
		function			unlock()
		{
			$Result = $this->PDO->query( 'UNLOCK TABLES' );

			$this->process_query_error( $Result , 'UNLOCK TABLES' );
		}

		/**
		*	Method starts transaction.
		*/
		function			start_transaction()
		{
			// setting autocommit off
			$Result = $this->PDO->query( 'SET AUTOCOMMIT = 0' );

			$this->process_query_error( $Result , 'SET AUTOCOMMIT = 0' );

			// starting transaction
			$Result = $this->PDO->query( 'START TRANSACTION' );

			$this->process_query_error( $Result , 'START TRANSACTION' );
		}

		/**
		*	Commiting transaction.
		*/
		function			commit()
		{
			// commit transaction
			$Result = $this->PDO->query( 'COMMIT' );

			$this->process_query_error( $Result , 'COMMIT' );

			// setting autocommit on
			$Result = $this->PDO->query( 'SET AUTOCOMMIT = 1' );

			$this->process_query_error( $Result , 'SET AUTOCOMMIT = 1' );
		}

		/**
		*	Rollback transaction.
		*/
		function			rollback()
		{
			// rollback transaction
			$Result = $this->PDO->query( 'ROLLBACK' );

			$this->process_query_error( $Result , 'ROLLBACK' );
		}

		/**
        *   Method builds insert query.
        */
        protected function	insert_query( $TableName , $Record )
        {
            $Query = 'INSERT '.$TableName.' SET '.$this->set_query( $Record );

            return( $Query );
        }

		/**
		*	Method inserts record.
		*/
		function			insert( $TableName , $Record )
		{
			$Query = $this->insert_query( $TableName , $Record );

			$Result = $this->PDO->query( $Query );

			$this->process_query_error( $Result , $Query );

			return( $this->PDO->lastInsertId() );
		}

		/**
		*	Method destroys connection.
		*/
		function			__destruct()
		{
			unset( $this->PDO );
		}
    }

?>