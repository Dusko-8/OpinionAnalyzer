@extends('layouts.app')

@section('content')
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/scrapers.css">
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/rawopinions.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <title>Posts Dropdown</title>
    <style>
    /* Určí minimálnu šírku pre stĺpec Akcie */
    #commentsTable th:last-child, #commentsTable td:last-child {
        min-width: 200px; /* Alebo akúkoľvek inú šírku, ktorú preferujete */
    }
    </style>
</head>
<body>
    <h1 class="page-title">Spravovať názory</h1>
    <div>
        <a href="{{ route('analyze') }}" id="Analyze">Pokračovať na Analýzu</a>
    </div>
    <div id="selectPosts">
        <label for="postSelect">Vyberte sociálnu otázku:</label>
        <select id="postSelect">
            <option value="" disabled selected>Vyberte</option>
            @foreach ($posts as $post)
                <option value="{{ $post->post_id }}" data-topics="{{ json_encode($post->topics) }}">
                    {{ $post->title }}
                </option>
            @endforeach
        </select>
    </div>
    <div id="topicsContainer"></div>
    
    <div id="commentsTableContainer" style="display:none;">
    <table id="commentsTable">
        <thead>
            <tr>
                <th>Komentáre</th>
                <th>Akcie</th>
            </tr>
        </thead>
        <tbody>
            <!-- Rows will be dynamically inserted here -->
        </tbody>
    </table>
</div>
</body>
</html>

<script>
    document.getElementById('postSelect').addEventListener('change', function() {
        const selectedPost = this.options[this.selectedIndex];
        const postId = selectedPost.value;

        if (!postId) {
            document.getElementById('commentsTableContainer').style.display = 'none';
            console.log('PostId is not defined');
            return;
        }

        loadComments(postId);
    });
    function loadComments(postId) {
        fetch(`/getComments?postid=${encodeURIComponent(postId)}`)
            .then(response => response.json())
            .then(comments => {
                console.log("Comments for the selected post:", comments);

                const commentsTable = document.getElementById('commentsTable').getElementsByTagName('tbody')[0];
                commentsTable.innerHTML = ''; // Clear existing comments

                comments.forEach(function(comment) {
                    const row = commentsTable.insertRow();
                    row.id = `commentRow${comment.comment_id}`;

                    const cell1 = row.insertCell(0);
                    const cell2 = row.insertCell(1);

                    cell1.textContent = comment.comment_text;
                    cell2.innerHTML = `<button class="edit-button" onclick="editComment(${comment.comment_id})"><i class="fa fa-edit"></i> Edituj</button>
                                       <button class="delete-button" onclick="deleteComment(${comment.comment_id})"><i class="fa fa-trash"></i> Zmazať</button>`
                });

                document.getElementById('commentsTableContainer').style.display = comments.length > 0 ? 'block' : 'none';
            })
            .catch(error => {
                console.error('Error fetching comments:', error);
                console.error('Error occurred fetching comments for post ID:', postId);
            });
    }
    // Define the deleteComment function
    function deleteComment(commentId) {

        console.log('Deleting comments for commentId :', commentId);
        // Confirm before deleting
        if (!confirm('Are you sure you want to delete this comment?')) {
            return;
        }
        
        fetch(`/deleteComment?commentId=${encodeURIComponent(commentId)}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data.message); // Log the success message
            // Remove the deleted comment's row from the table
            document.querySelector(`#commentRow${commentId}`).remove();

            loadComments(postId);
        })
        .catch(error => console.error('Error:', error));
    }

    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            deleteComment(this.getAttribute('data-comment-id'));
        });
    });
</script>

@endsection