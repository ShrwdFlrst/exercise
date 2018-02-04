# Github API Search

## Requirements

- PHP 5.6+
- PHP CURL extension

## Running 

Generate a [Github token](https://github.com/settings/tokens)

    php ./php/github_search.php GITHUB_TOKEN "skin care"
    php ./php/github_search.php GITHUB_TOKEN "lipstick"
    php ./php/github_search.php GITHUB_TOKEN "mascara"
    
    
### Notes/Improvements

- To keep it the code compatible with 5.6 I've kept it simple, normally I'd use the GuzzleHttp package for handling CURL requests, 
- Composer support could be added
- Better error handling
- I couldn't get exact phrase search to work, tried using quotes but that returned errors; url encodin the search query worked
- Refactor pagination to use Response header links
