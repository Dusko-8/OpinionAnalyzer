import os
import json
import scrapy
from collections import defaultdict
from scrapy.crawler import CrawlerProcess
from scrapy.utils.project import get_project_settings
import sys
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
# Your RedditSearchSpider class here
class RedditSearchSpider(scrapy.Spider):
    name = 'reddit_search_spider'
    queryCz = ""
    querySK = ""
    subreddits  = None
    lang = ""
    def __init__(self, query, lang ="SK" , *args, **kwargs):
        super(RedditSearchSpider, self).__init__(*args, **kwargs)
        subredditsSK = ['Bratislava', 'Slovakia']
        subredditsCZ = ['czech']
        self.scraped_data = []
        if(lang == "SK"):
            self.querySK = query
            self.subreddits  = subredditsSK
            self.lang = lang
        elif( lang == "CZ"):
            self.queryCz = query
            self.subreddits  = subredditsCZ
            self.lang = lang
        else:
            print("Not supported language")
        
    
    def start_requests(self):
        # For subreddits
        for subreddit in self.subreddits:
            if(self.lang == "SK"):
                url = f'https://old.reddit.com//r/{subreddit}/search/?q={self.querySK}&restrict_sr=1'
            elif(self.lang == "CZ"):
                url = f'https://old.reddit.com//r/{subreddit}/search/?q={self.queryCZ}&restrict_sr=1'
            yield scrapy.Request(url, self.parse, meta={'subreddit': subreddit})
        #DElet e later
        ## For Czech subreddits
        #for subreddit in self.subredditsCZ:
        #    url = f'https://old.reddit.com//r/{subreddit}/search/?q={self.queryCZ}&restrict_sr=1'
        #    yield scrapy.Request(url, self.parse, meta={'subreddit': subreddit})
   
    def parse(self, response):
        # Získanie názvu subredditu z meta dát
        subreddit_name = response.meta['subreddit']

        # Select all the anchor tags that have post titles
        posts= response.xpath('//a[@class="search-title may-blank"]')[:2]
        #//div[contains(@class, 'comment')]//div[@class='md']/p 
        for post in posts:
            title = post.xpath('string(.)').get().strip()
            post_url = post.xpath('@href').get()
            # Make a request to the post URL to extract comments
            yield scrapy.Request(
                response.urljoin(post_url),
                callback=self.parse_post,
                meta={
                    'title': title,
                    'subreddit': subreddit_name,
                }
            )

    def parse_post(self, response):
        # Extract comments using the given XPath
        comments = response.xpath('//div[contains(@class, "comment")]//div[@class="md"]/p')
        comment_texts = [comment.xpath('string(.)').get().strip() for comment in comments]
        # Store the scraped data in a dictionary
        scraped_data = {
            'title': response.meta['title'],
            'subreddit': response.meta['subreddit'],
            'post_url': response.url,
            'comments': comment_texts
        }

        # Print the scraped data
        self.scraped_data.append(scraped_data)
        # Yield the scraped data (for processing or printing)
        # Add the closed method

    def closed(self, reason):
        # This method is called when the spider is closed
        output = {"posts": self.scraped_data}
        json_output = json.dumps(output, ensure_ascii=False)
        print(json_output)
        # Optionally, write json_output to a file
# Function to run the Reddit spider
def run_reddit_spider(query, lang):
    process = CrawlerProcess()
    process.crawl(RedditSearchSpider, query=query, lang=lang)
    process.start()  # This will block until the spider finishes

# Main controller function
def run_controller(query, lang):
    run_reddit_spider(query, lang)

# This allows the script to be run with command line arguments
if __name__ == '__main__':
    query = sys.argv[1] if len(sys.argv) > 1 else 'Členstvo v EU'
    #print(query)
    lang = sys.argv[2] if len(sys.argv) > 2 else 'SK'
    run_reddit_spider(query, lang)
