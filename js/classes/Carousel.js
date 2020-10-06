class Carousel extends Form {
    constructor(element) {
        super();
        this.board = element;
    }

    handle() {
        // list all cards
        this.cards = this.board.querySelectorAll('.card');

        // get top card
        this.topCard = this.cards[this.cards.length - 1];

        // get next card
        this.nextCard = this.cards[this.cards.length - 2];

        // if at least one card is present
        if (this.cards.length > 0) {
            // set default top card position and scale
            this.topCard.style.transform = 'translateX(-50%) translateY(-50%) rotate(0deg) rotateY(0deg) scale(1)';

            // destroy previous Hammer instance, if present
            if (this.hammer) this.hammer.destroy();

            // listen for tap and pan gestures on top card
            this.hammer = new Hammer(this.topCard);
            this.hammer.add(new Hammer.Tap());
            this.hammer.add(new Hammer.Pan({
                position: Hammer.position_ALL,
                threshold: 0
            }));

            // pass events data to custom callbacks
            this.hammer.on('tap', (e) => {
                this.onTap(e);
            });
            this.hammer.on('pan', (e) => {
                this.onPan(e);
            });
        }
    }

    onTap(e) {
        // get finger position on top card
        let propX = (e.center.x - e.target.getBoundingClientRect().left) / e.target.clientWidth;

        // get rotation degrees around Y axis (+/- 15) based on finger position
        let rotateY = 15 * (propX < 0.05 ? -1 : 1);

        // enable transform transition
        this.topCard.style.transition = 'transform 100ms ease-out';

        // apply rotation around Y axis
        this.topCard.style.transform = 'translateX(-50%) translateY(-50%) rotate(0deg) rotateY(' + rotateY + 'deg) scale(1)';

        // wait for transition end
        setTimeout(() => {
            // reset transform properties
            this.topCard.style.transform = 'translateX(-50%) translateY(-50%) rotate(0deg) rotateY(0deg) scale(1)';
        }, 100)
    }

    onPan(e) {
        if (!this.isPanning) {
            this.isPanning = true;

            // remove transition properties
            this.topCard.style.transition = null;
            if (this.nextCard) this.nextCard.style.transition = null;

            // get top card coordinates in pixels
            let style = window.getComputedStyle(this.topCard);
            let mx = style.transform.match(/^matrix\((.+)\)$/);
            this.startPosX = mx ? parseFloat(mx[1].split(', ')[4]) : 0;
            this.startPosY = mx ? parseFloat(mx[1].split(', ')[5]) : 0;

            // get top card bounds
            let bounds = this.topCard.getBoundingClientRect();

            // get finger position on top card, top (1) or bottom (-1)
            this.isDraggingFrom = (e.center.y - bounds.top) > this.topCard.clientHeight / 2 ? -1 : 1;
        }

        // get new coordinates
        let posX = e.deltaX + this.startPosX;
        let posY = e.deltaY + this.startPosY;

        // get ratio between swiped pixels and the axes
        let propX = e.deltaX / this.board.clientWidth;
        let propY = e.deltaY / this.board.clientHeight;

        // get swipe direction, left (-1) or right (1)
        let dirX = e.deltaX < 0 ? -1 : 1;

        // get degrees of rotation, between 0 and +/- 45
        let deg = this.isDraggingFrom * dirX * Math.abs(propX) * 45;

        // get scale ratio, between .95 and 1
        let scale = (95 + (5 * Math.abs(propX))) / 100;

        // move and rotate top card
        this.topCard.style.transform = 'translateX(' + posX + 'px) translateY(' + posY + 'px) rotate(' + deg + 'deg) rotateY(0deg) scale(1)';

        // scale up next card
        if (this.nextCard) this.nextCard.style.transform = 'translateX(-50%) translateY(-50%) rotate(0deg) rotateY(0deg) scale(' + scale + ')';

        if (e.isFinal) {
            this.isPanning = false;
            let successful = false;

            // set back transition properties
            this.topCard.style.transition = 'transform 200ms ease-out';
            if (this.nextCard) this.nextCard.style.transition = 'transform 100ms linear';

            // check threshold and movement direction
            if (propX > 0.25 && e.direction == Hammer.DIRECTION_RIGHT) {
                successful = true;
                // get right border position
                posX = this.board.clientWidth;
            } else if (propX < -0.25 && e.direction == Hammer.DIRECTION_LEFT) {
                successful = true;
                // get left border position
                posX = -(this.board.clientWidth + this.topCard.clientWidth);
            } else if (propY < -0.25 && e.direction == Hammer.DIRECTION_UP) {
                successful = true;
                // get top border position
                posY = -(this.board.clientHeight + this.topCard.clientHeight);
            }

            if (successful) {
                // throw card in the chosen direction
                this.topCard.style.transform = 'translateX(' + posX + 'px) translateY(' + posY + 'px) rotate(' + deg + 'deg)';
                this.handleSwipe(dirX);

                // wait transition end
                setTimeout(() => {
                    // remove swiped card
                    this.board.removeChild(this.topCard);
                    // add new card
                    this.push();
                    // handle gestures on new top card
                    this.handle();
                }, 200);
            } else {
                // reset cards position and size
                this.topCard.style.transform = 'translateX(-50%) translateY(-50%) rotate(0deg) rotateY(0deg) scale(1)';
                if (this.nextCard) this.nextCard.style.transform = 'translateX(-50%) translateY(-50%) rotate(0deg) rotateY(0deg) scale(0.95)';
            }
        }
    }

    push() {
        if(this.movies && this.movies.length){
            let movieData = this.movies[0];
            
            this.form_data.append('action', 'validateMovie');
            this.form_data.append('movie_id', this.movies[0].id);
            let globalScope = this;

            this.ajaxCall('../../php/data/movies.php', this.form_data, async function(response){
                if(response == 'error'){
                    globalScope.get();
                } else {
                    // Main element
                    let card = document.createElement('div');
                    card.classList.add('card');
                    card.style.backgroundImage = "url('http://image.tmdb.org/t/p/original/"+ movieData.poster_path +"')";
        
                    // Info button
                    let infoButton = document.createElement('button');
                    infoButton.classList.add('movie-info-button');
                    infoButton.classList.add('btn-success');
                    infoButton.innerHTML = '<i class="fas fa-question-circle"></i>';
                    card.append(infoButton);
                    globalScope.board.insertBefore(card, globalScope.board.firstChild);
        
                    // JSON data
                    let infoInput = document.createElement('input');
                    infoInput.setAttribute("json_data", JSON.stringify(movieData));
                    infoInput.setAttribute("type", "hidden");
                    card.append(infoInput);
        
                    // On card click event
                    $(card).on('click', '.movie-info-button', function(){
                        globalScope.showMovieInfo(JSON.parse($(card).find('input').attr('json_data')));
                    });
                    
                    // handle gestures
                    globalScope.movies.shift();
                    globalScope.handle();
                }
            });
        } else {
            this.get();
        }
    }

    get () {
        let globalScope = this;
        let pageNumber = Math.floor(Math.random() * 100) + 1;
        this.apiKey = "06454454e69fbc41ba407d51cd27c80c";
        this.apiUrl = "https://api.themoviedb.org/4/list/"+ pageNumber +"?page=1&api_key=" + this.apiKey;
        
        $.get( this.apiUrl, function( data ) {
            if(data){
                globalScope.movies = data.results;
                globalScope.push();
            }
        });
    }

    showMovieInfo (movie) {
        let element = '<div class="user-modal-content">' +
            '<dl class="row">' +
            '<dt class="col-sm-3">Name</dt>' +
            '<dd class="col-sm-9">'+ movie.title +'</dd>' +

            '<dt class="col-sm-3">Description</dt>' +
            '<dd class="col-sm-9">'+ (movie.overview ?? movie.description) +'</dd>' +

            '<dt class="col-sm-3">Release date</dt>' +
            '<dd class="col-sm-9"><p>'+ movie.release_date +'</p></dd>';

        if(movie.poster_path){
            element += '<dt class="col-sm-3">Profile image</dt>' +
            '<dd class="col-sm-9"><p><img style="width: 100%;" class="friend-image" src="http://image.tmdb.org/t/p/original/'+ movie.poster_path +'" alt="user-image"/></p></dd>';
        }

        element += '</dl>' +
            '</div>'
        ;

        $('#movie-modal').html(element);
        $('#movie-modal-container').modal('show');
    }

    handleSwipe (direction) {
        let globalScope = this;
        let element = this.hammer.element;
        let movie = JSON.parse($(element).find('input').attr('json_data'));

        this.form_data.append('action', 'insertLoadedMovie');
        this.form_data.append('type', direction);
        $.each(movie, function(key, value){
            globalScope.form_data.append(key, value);
        });
        
        this.ajaxCall('../../php/data/movies.php', this.form_data, async function(response){
            if(response){
                // Successfully updated

            }
        });
    }
}

let board = document.querySelector('#board');

let carousel = new Carousel(board);

// carousel.get();
// carousel.push();