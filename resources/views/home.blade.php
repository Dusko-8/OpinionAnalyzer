@extends('layouts.app')

@section('content')
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/scrapers.css">
    <link rel="stylesheet" href="/css/home.css">
    <!-- Other head elements like title, scripts, etc. -->
</head>

<div class="container">
    <h1 class="page-title">Získavanie dát</h1>
    <div class="container custom-container">
        <div>
            <a href="{{ route('rawopinions') }}" id="Spravuj">Pokračuj na správu názorov</a>
        </div>
        <div class="mb-3">
            <label for="socialQuestionInput" class="form-label">Sociálna otázka</label>
            <input type="text" class="form-control" id="socialQuestionInput" placeholder="Zadajte sociálnu otázku">
        </div>
        <div class="mb-3">
            <label for="languageSelect" class="form-label">Zvoľte jazyk pre ktorý hľadať sociálnu otázku</label>
            <select class="form-select" id="languageSelect">
                <option value="SK">Slovensky</option>
                <option value="CZ">Česky</option>
            </select>
        </div>        
        <div>
            <label class="form-label">Facebook e-mail a heslo</label>
            <div class="input-group mb-3">
                <input type="email" class="form-control" placeholder="E-mail" id="email">
            </div>
            <div class="input-group mb-3">
                <input type="password" class="form-control" placeholder="Heslo" id="password">
            </div>
        </div>
        <button onclick="runScrapeReddit()" class="btn btn-primary custom-button">Získať dáta z Redditu</button>
        <button onclick="runScrapeFacebook()" class="btn btn-success custom-button">Získať dáta z Facebooku</button>
    </div>
    <div class="container custom-container">
        <div id="outputDiv" style="display: none;">Načítavam...</div>
        <button onclick="saveData()" class="btn btn-primary">Uložiť dáta</button>
    </div>

    <script>
        let postExists = false;
        let globalComments = [];
        function runScrapeReddit() {

            globalComments = [];

            const socialQuestion = document.getElementById('socialQuestionInput').value;
            const language = document.getElementById('languageSelect').value;

            if (!socialQuestion || !language) {
                alert('Please fill in all fields.');
                return;
            }

            // Make outputDiv visible and set to "Loading..."
            const outputDiv = document.getElementById('outputDiv');
            outputDiv.style.display = 'block';
            outputDiv.innerHTML = 'Loading...';

            const url = `/scrape-reddit?question=${encodeURIComponent(socialQuestion)}&language=${encodeURIComponent(language)}`;
        
            fetch(url)
            .then(response => {
                // Check the content type of the response
                const contentType = response.headers.get("content-type");

                if (contentType && contentType.includes("application/json")) {
                    // If it's JSON, parse it as JSON
                    return response.json().then(data => ({ isJson: true, data }));
                } else {
                    // Otherwise, treat it as text
                    return response.text().then(data => ({ isJson: false, data }));
                }
            })
            .then(result => {
                if (result.isJson && result.data.posts && result.data.posts.length > 0) {
                    //empty string
                    let outputHtml = '';
                
                    // Loop through each post 
                    result.data.posts.slice(0, 2).forEach(post => {
                        console.log(`Comments for post titled "${post.title}":`); // Log the title of the post
                
                        // Log all comments for the post
                        post.comments.forEach(comment => {
                            console.log(comment); // Log each individual comment
                        });
                        
                        // concatenate all comments from the post only once to the globalComments
                        globalComments = globalComments.concat(post.comments);
                        console.log('---------GLOBAL COMMENTS--------');
                        console.log(globalComments);

                        outputHtml += `<h2>${post.title}</h2>`;
                        outputHtml += `<h4>${post.subreddit}</h4><ul>`;
                        // Take the first five comments of each post
                        post.comments.slice(0, 5).forEach(comment => {
                            outputHtml += `<li>${comment}</li>`;
                        });
                    
                        outputHtml += '</ul>';
                    });
                
                    // Set the innerHTML of the outputDiv to our generated HTML
                    document.getElementById('outputDiv').innerHTML = outputHtml;
                } else {
                    // Handle text data or errors differently
                    document.getElementById('outputDiv').innerHTML = 'No comments found or invalid format.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('outputDiv').innerHTML = 'Error pri sťahovaní.';
            });
        }

        function runScrapeFacebook() {

            // Make outputDiv visible and set to "Loading..."
            const outputDiv = document.getElementById('outputDiv');
            outputDiv.style.display = 'block';
            outputDiv.innerHTML = 'Loading...';

            const socialQuestion = document.getElementById('socialQuestionInput').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
           
            if (!socialQuestion || !email || !password) {
                alert('Please fill in all fields.');
                return;
            }

            const url = `/scrape-facebook?topic=${encodeURIComponent(socialQuestion)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`;

            fetch(url)
            .then(response => {
                // Check the content type of the response
                const contentType = response.headers.get("content-type");
                
                if (contentType && contentType.includes("application/json")) {
                    // If it's JSON, parse it as JSON
                    return response.json().then(data => ({ isJson: true, data }));
                } else {
                    // Otherwise, treat it as text
                    return response.text().then(data => ({ isJson: false, data }));
                }
            })
            .then(result => {
                
                console.log("Received data:", result.data); // Log the complete data for inspection
                     
                // Assuming result.data is directly the array containing the objects with TITLE, DESCRIPTION, and OPINIONS
                if (Array.isArray(result.data) && result.data.length > 0) {
                    let outputHtml = '';  // Initialize HTML content string
                    

                    result.data.forEach(item => {
                        item.OPINIONS.forEach(opinion => {
                            Object.keys(opinion).forEach(key => {
                                globalComments.push(opinion[key]);  // Appending each opinion to globalComments
                            });
                        });
                        console.log("DESCRIPTION:", item.DESCRIPTION);  // Log the description
                        
                        // Build HTML for Title and Description
                        outputHtml += `<h1>${item.TITLE}</h1>`;
                        outputHtml += `<p>${item.DESCRIPTION}</p>`;
                        // Check if OPINIONS is present and correctly formatted
                        if (item.OPINIONS && Array.isArray(item.OPINIONS) && item.OPINIONS.length > 0) {
                            // Begin numbered list for opinions
                            outputHtml += '<ol>';
                                        
                            item.OPINIONS.forEach(opinion => {
                                Object.keys(opinion).forEach(key => {
                                    outputHtml += `<li> ${opinion[key]}</li>`;    
                                });
                            });
                        
                            // Close numbered list
                            outputHtml += '</ol>';
                        } else {
                            outputHtml += '<p>žiadne názory neboli získané.</p>';
                        }
                    });
                    console.log("outputDiv",outputHtml);
                    // Set the innerHTML of the outputDiv to the generated HTML
                    document.getElementById('outputDiv').innerHTML = outputHtml;
                    console.log("Inner HTML",document.getElementById('outputDiv').innerHTML);
                } else {
                    console.log("Data is missing or the structure is incorrect");
                    document.getElementById('outputDiv').innerHTML = 'Štruktúra dát je chýbná skúste znovu.';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('outputDiv').innerHTML = 'Chyba pri spracovaní dát skúste znovu.';
            });
        }
        // New function to check post existence
        document.addEventListener('DOMContentLoaded', function() {
            // Debounce function to limit the rate at which a function is called
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = function() {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            };
        
            // Function to check post existence by calling a Laravel route
            async function checkPostExistence() {
                const socialQuestion = document.getElementById('socialQuestionInput').value;
                // Since you're only using the title for checking existence, language and platform are not sent
                if (!socialQuestion.trim()) return; // Skip empty queries
            
                // Construct the URL for the GET request
                const url = `/check-post-existence?title=${encodeURIComponent(socialQuestion)}`;
            
                try {
                    const response = await fetch(url);
                    const result = await response.json();
                
                    // Here you can update your UI based on the existence of the post
                    if (result.exists) {
                        postExists=true;
                        console.log('Post exists.');
                        // Update your UI to show that the post exists
                    } else {
                        postExists=false;
                        console.log('Post does not exist.');
                        // Update your UI accordingly
                    }
                } catch (error) {
                    console.error('Error checking post existence:', error);
                }
            }
        
            // Add the event listener to the socialQuestionInput with debounce
            const socialQuestionInput = document.getElementById('socialQuestionInput');
            socialQuestionInput.addEventListener('input', debounce(checkPostExistence, 1500)); // Adjust the delay as needed
        });

        function saveData() {
            console.log('-----------------------------------');
            console.log('Saving comments:', globalComments);

            const title = document.getElementById('socialQuestionInput').value;
            // Add other data you might want to save, like language or any additional info

            // Log the global comments just before attempting to save
            
            if (globalComments.length === 0) {
                alert('No comments to save. Please scrape data first.');
                return;
            }
            
            // Validate title or other fields if needed
            if (!title) {
                alert('Title is required.');
                return;
            }
        
            fetch('/store-post', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Ensure CSRF token is included for Laravel POST requests
                },
                body: JSON.stringify({
                    title: title,
                    comments: globalComments
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                alert('Post saved successfully');
                globalComments = [];
                // Perform any additional actions on success (e.g., clearing form, updating UI)
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('Failed to save the post');
            });
        }

    </script>
</div>

@endsection