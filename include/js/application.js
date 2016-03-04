/**
*	Замена создержимого блока на progress bar.
*/
function			progress_control( Selector )
{
	var			Height = jQuery( Selector ).height();
	Height = Height < 50 ? 400 : Height;
	var			ProgressContent = '<div style=" margin: 0 auto; display: block; width: 32px; height: ' + Height + 'px;"><div class="valign_child"><div class="jsdialogs-loading" style="float: none; margin-right : 0px;"></div></div><div class="valign_helper"></div></div>';

	jQuery( Selector ).html( ProgressContent );
}

/**
*	Аяксовый запрос с выгрузкой результата.
*/
function			ajax_request( URL , Data , Selector , Async , Success )
{
	if( !Success )
	{
		Success = function( Result )
		{
			if( jQuery( Selector ).length )
			{
				jQuery( Selector ).html( Result );
			}
		}
	}

	jQuery.ajax(
		{
			'method' : 'POST' , 
			'url' : URL , 
			'data' : Data , 
			'async' : Async , 
			'success' : Success
		}
	);
}

/**
*	Добавление вкладки.
*/
function			add_tab( TabTitle )
{
	var			label = TabTitle , 
				id = "tabs-" + TabCounter , 
				li = jQuery( TabTemplate.replace( /#\{href\}/g, "#" + id ).replace( /#\{label\}/g, label ) );

	jQuery( "#tabs" ).find( ".ui-tabs-nav" ).append( li );
	jQuery( "#tabs" ).append( "<div id='" + id + "'>Content</div>" );
	jQuery( "#tabs" ).tabs( "refresh" );
	jQuery( "#tabs" ).tabs( { active : jQuery( '#tabs' ).find( 'a.ui-tabs-anchor' ).length - 1 } );

	TabCounter++;

	return( id );
}

/**
*	Создание вкладки с подтягиванием её содержимого через аякс.
*/
function			create_ajax_tab( URL , Data , Title )
{
	var			TabId = add_tab( Title );

	progress_control( '#' + TabId );

	ajax_request( URL , Data , '#' + TabId );
}

/**
*	Объекты и переменные для создания табового интерфейса.
*/
var					TabCounter = 2 , 
					TabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Закрыть вкладку</span></li>";

/**
*	Функция подтверждения намеренья выполнить действие.
*/
var					ShureFunction = function(){};

/**
*	Функция открытия диалога подтверждения действия.
*/
function			open_shure_dialog( Message , Function )
{
	jQuery( '#shure-dialog' ).dialog( 'open' );
	jQuery( '#shure-dialog' ).html( Message );

	ShureFunction = function()
	{
		Function();
	}
}

/**
*	Компиляция данных.
*/
function			dispatch_fields( GridGenerationData )
{
	var			Data = {};

	for( var i = 0 ; i < GridGenerationData.length ; i++ )
	{
		var			ItemName = jQuery( GridGenerationData[ i ] ).attr( 'name' );

		if( ItemName.indexOf( '[]' ) != -1  )
		{
			if( !Data[ ItemName.replace( '[]',  '' ) ] )
			{
				Data[ ItemName.replace( '[]',  '' ) ] = [];
			}
			Data[ ItemName.replace( '[]',  '' ) ].push( jQuery( GridGenerationData[ i ] ).val() );
		}
		else
		{
			Data[ ItemName ] = jQuery( GridGenerationData[ i ] ).val();
		}
	}

	return( Data );
}

/**
*	Компиляция данных.
*/
function			get_grid_generation_data()
{
	var			TabId = jQuery( "#tabs" ).find( 'div:visible.ui-tabs-panel' ).attr( 'id' );

	var			GridGenerationData = jQuery( "#" + TabId ).find( 'input[type=hidden]' );

	return( dispatch_fields( GridGenerationData ) );
}

/**
*	Функция удаления сущности.
*/
function			delete_entity( HandlerURL , EntityId )
{
	var			Data = get_grid_generation_data();

	return(
		function()
		{
			var			TabId = jQuery( "#tabs" ).find( 'div:visible.ui-tabs-panel' ).attr( 'id' );
			progress_control( '#' + TabId );

			ajax_request( 
				HandlerURL , 
				{ 
					'command' : 'delete' , 'id' : EntityId 
				} , '' , true , 
				function( Result )
				{
					jQuery( '#result-dialog' ).dialog( 'open' );
					jQuery( '#result-dialog' ).html( Result );

					ajax_request( HandlerURL , Data , '#' + TabId );
				}
			);
		}
	);
}

jQuery(
	function()
	{
		jQuery( "#tabs" ).tabs();

		jQuery( "#tabs" ).delegate( 
			"span.ui-icon-close" , "click" , 
			function()
			{
				var			PanelId = jQuery( this ).closest( "li" ).remove().attr( "aria-controls" );
				jQuery( "#" + PanelId ).remove();
				jQuery( "#tabs" ).tabs( "refresh" );
			}
		);

		jQuery( "#tabs" ).find( 'li' ).click(
			function( e )
			{
				var			TabId = jQuery( e.currentTarget ).find( 'a' ).attr( 'id' ).replace( 'ui-id-' , '' );
				jQuery.cookie( "active-tab" , TabId - 1 );
			}
		);

		jQuery( '#result-dialog' ).dialog( 
			{ 
				'modal' : true , 'autoOpen' : false , 'width' : '400' , 'height' : '150' , 
				'position' : { my : 'center' , at : 'center' , of : window } , 
				'buttons' : 
				{
					'OK' : function( Fn )
					{
						jQuery( '#result-dialog' ).dialog( 'close' );
					}
				}
			}
		);

		jQuery( '#shure-dialog' ).dialog( 
			{ 
				'modal' : true , 'autoOpen' : false , 'width' : '400' , 'height' : '150' , 
				'position' : { my : 'center' , at : 'center' , of : window } , 
				'buttons' : 
				{
					'OK' : function( Fn )
					{
						jQuery( '#shure-dialog' ).dialog( 'close' );
						ShureFunction();
						ShureFunction = function(){};
					}
					 , 
					'Отмена' : function()
					{
						jQuery( '#shure-dialog' ).dialog( 'close' );
						ShureFunction = function(){};
					}
				}
			}
		);
    }
);

function			exit()
{
	open_shure_dialog( "Вы уверены, что хотите выйти из системы?" , function(){ document.location = './logout'; } );
}
