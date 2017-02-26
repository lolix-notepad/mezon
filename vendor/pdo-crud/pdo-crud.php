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
		protected function	process_query_error( $Result )
		{
			if( $Result === false )
			{
				$ErrorInfo = $this->PDO->errorInfo();
				throw( 
					new Exception( 
						$ErrorInfo[ 2 ].' in statement '.
							$this->select_query( $Fields , $TableNames , $Where , $From , $Limit ) 
					) 
				);
			}
		}

        /**
        *   Getting records.
        */
        function            select( $Fields , $TableNames , $Where = '1 = 1' , $From = 0 , $Limit = 1000000 )
        {
            $Result = $this->PDO->query( $this->select_query( $Fields , $TableNames , $Where , $From , $Limit ) );

			$this->process_query_error( $Result );

            return( $Result->fetchAll() );
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
			$Result = $this->PDO->query( $this->delete_query( $TableName , $Where , $Limit ) );

			$this->process_query_error( $Result );
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

			$Query = 'LOCK TABLES '.implode( ', ' , $Query );

			return( $Query );
		}

		/**
		*	Method locks tables
		*/
		function			lock( $Tables , $Modes )
		{
			$Result = $this->PDO->query( $rhis->lock_query() );

			$this->process_query_error( $Result );
		}

		/**
		*	Method unlocks locked tables.
		*/
		function			unlock()
		{
			$Result = $this->PDO->query( 'UNLOCK TABLES' );

			$this->process_query_error( $Result );
		}

		/**
		*	Method starts transaction.
		*/
		function			start_transaction()
		{
			// setting autocommit off
			$Result = $this->PDO->query( 'SET AUTOCOMMIT = 0' );

			$this->process_query_error( $Result );

			// starting transaction
			$Result = $this->PDO->query( 'START TRANSACTION' );

			$this->process_query_error( $Result );
		}

		/**
		*	Commiting transaction.
		*/
		function			commit()
		{
			// commit transaction
			$Result = $this->PDO->query( 'COMMIT' );

			$this->process_query_error( $Result );
		}

		/**
		*	Rollback transaction.
		*/
		function			rollback()
		{
			// rollback transaction
			$Result = $this->PDO->query( 'ROLLBACK' );

			$this->process_query_error( $Result );
		}
    }

?>