class MoviesNav extends Carousel {
    constructor () {
        super();
        this.board = document.querySelector('#board');
        this.setEvents();
    }

    setEvents(){
        let globalScope = this;
        $('.movies-container').find('.movies-nav-button').on('click', function(){
            let value = $(this).val();
            if(value){
                globalScope[value]();
            }
        });

        $(document).on('click', '.card-item', function(){
            let movie = globalScope.urldecode($(this).find('input').attr('json_data'));
            movie = JSON.parse(movie);
            console.log(movie);
            globalScope.showMovieInfo(movie);
        });
    }

    movieMatcher(){
        $('#movies-content').hide();
        $('#board').show();
        this.get();
        this.push();
    }

    fetchLikedMovies(){
        this.table = 'liked_movies';
        this.movieFetch();
    }

    fetchDislikedMovies () {
        this.table = 'disliked_movies';
        this.movieFetch();
    }

    movieFetch(){
        $('#board').hide();
        $('#movies-content').show();

        this.form_data.append('action', 'fetchTypedMovies');
        this.form_data.append('table', this.table);
        this.ajaxCall('../../php/data/movies.php', this.form_data, async function(response){
            if(response){
                response = JSON.parse(response);
                $('#movies-content').html(response);
            }
        });
    }

    urldecode(str) {
        return decodeURIComponent((str+'').replace(/\+/g, '%20'));
    }
}

new MoviesNav();