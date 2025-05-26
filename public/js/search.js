$(document).ready(function() {
    var table = $('#articlesTable').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: '/api/articles',  // adapte selon ta route si besoin
            type: 'POST',
            data: function(d) {
                d.search.value = $('#searchInput').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'title' },
            { data: 'categories' },
            { data: 'commentsCount' },
            { data: 'likesCount' },
            { data: 'createdAt' },
            { data: 'actions' }
        ],
        order: [[0, 'desc']]
    });

    $('#searchInput').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '/api/articles/search', // adapte la route
                dataType: 'json',
                data: { q: request.term },
                success: function(data) {
                    response(data.results.map(function(article) {
                        return { label: article.title, value: article.title };
                    }));
                }
            });
        },
        minLength: 2,
        select: function(event, ui) {
            $('#searchInput').val(ui.item.value);
            table.search(ui.item.value).draw();
            return false;
        }
    });

    $('#searchInput').on('keyup', function() {
        if ($(this).val().length === 0) {
            table.search('').draw();
        }
    });
});
