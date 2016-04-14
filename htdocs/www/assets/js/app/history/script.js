

var Carousel = function( $target )
{
    // Set DOM
    this.$                      = {};
    this.$.history              = $target;
    this.$.pagination_container = this.$.history.find( 'ul' );
    this.$.pagination_items     = this.$.pagination_container.find( 'li' );
    this.$.slides_container     = this.$.history.find( '.wrap ul' );
    this.$.slides_items         = this.$.slides_container.find( 'li' );

    // Set properties
    this.count = this.$.slides_items.length;

    // Init
    this.init_events();
};

// Properties
Carousel.prototype.index = 0;
Carousel.prototype.count = 0;
Carousel.prototype.width = 210;

// Methods
Carousel.prototype.init_events = function()
{
    var that = this;

    /*
    // Next click
    document.on( 'keydown', function()
    {
        if (isArrowKey(39)) {
            that.next();

            return false;
        }
    } );

    // Prev click
    document.on( 'keydown', function()
    {
        if (isArrowKey(37)) {
            that.prev();

            return false;
        }
    } );

    */


    // Pagination click
    this.$.pagination_items.on( 'click', function()
    {
        var $pagination = $( this ),
            index       = $pagination.index();

        that.go_to( index );

        return false;
    } );
};

Carousel.prototype.go_to = function( index )
{
    // Limits
    if( index > this.count - 1 )
        index = 0;
    else if( index < 0 )
        index = this.count - 1;

    // Same
    if( index === this.index )
        return;

    // Animate
    this.$.slides_container.animate( {
        left : - index * this.width
    } );

    // Save new index
    this.index = index;

    // Update pagination
    this.update_pagination();
};

Carousel.prototype.next = function()
{
    this.go_to( this.index + 1 );
};

Carousel.prototype.prev = function()
{
    this.go_to( this.index - 1 );
};

Carousel.prototype.update_pagination = function()
{
    this.$.pagination_items.removeClass( 'active' );
    this.$.pagination_items.eq( this.index ).addClass( 'active' );
};

var $carousels = $( '.carousel' );

$carousels.each( function()
{
    var $carousel = $( this ),
        carousel  = new Carousel( $carousel );
} );
