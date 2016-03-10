/**
*	Replace any DOM element with the progress bar.
*/
function			progress_control( Selector )
{
	var			Height = jQuery( Selector ).height();
	Height = Height < 50 ? 400 : Height;
	var			ProgressContent = '<div style=" margin: 0 auto; display: block; width: 32px; height: ' + Height + 'px;"><div class="valign_child"><div class="jsdialogs-loading" style="float: none; margin-right : 0px;"></div></div><div class="valign_helper"></div></div>';

	jQuery( Selector ).html( ProgressContent );
}

/**
*	AJAX request function.
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
*	Additing tab.
*/
function			add_empty_tab( TabTitle )
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
*	Creating tab with the AJAX content.
*/
function            open_entity_list_in_tab( URL , Title )
{
    add_empty_tab( Title );

    //progress_control( Selector );
}

/**
*	Global objects.
*/
var					TabCounter = 2 , 
					TabTemplate = "<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' role='presentation'>Закрыть вкладку</span></li>";

/**
*	Confirmation function.
*/
var					ShureFunction = function(){};

/**
*	Function shows confirmation dialog.
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
