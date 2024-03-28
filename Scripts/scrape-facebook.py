from selenium.webdriver.chrome.service import Service
import sys
import io

import os
from selenium import webdriver
from selenium.webdriver.common.by import By
import keyboard
import time
from random import uniform
import json
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.support import expected_conditions as EC


chrome_path = "P:\\chromedriver\\chromedriver.exe"
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

options = Options()
options.add_argument('--disable-gpu')
options.add_argument("--disable-notifications")

driver = webdriver.Chrome(service=Service(chrome_path), options=options)
#driver.execute_cdp_cmd("Network.setCacheDisabled", {"cacheDisabled":True})

if len(sys.argv) > 1:
    query = sys.argv[1]  # Use the passed argument as the query
    email = sys.argv[2]
    password = sys.argv[3]
else:
    query = "Členstvo v Európskej únií"  # Default query if no argument is passed
    email = "perkins5@azet.sk"
    password = "messi10"
    #exit(1)
#print("query - "+query)
#FUNCTIONS
def slow_type(element, text, min_delay=0.1, max_delay=0.5):
    for char in text:
        element.send_keys(char)
        time.sleep(uniform(min_delay, max_delay))

def login(email,pas):
    cookies = driver.find_element(By.XPATH, "//button[text()='Povoliť všetky cookies']")
    cookies.click()

    email_input = driver.find_element(By.XPATH, '//input[@id="email"]')
    slow_type(email_input, email)
    pass_input = driver.find_element(By.XPATH, '//input[@id="pass"]')
    slow_type(pass_input, pas)

    login = driver.find_element(By.XPATH, "//button[@name='login']")
    login.click()
    time.sleep(5)

#GLOBALVARS
ACTUAL_NUMBER_OF_POSTS = 0
NUMBER_OF_COMMENTS = 0
NUMBER_OF_POSTS = 0

#SETTINGS
MAX_NUMBER_OF_POSTS = 1
RECENT_POSTS = True

driver.get("https://www.facebook.com")

#print("Waiting for CTRL+ALT+S...")

#Wait until CTRL+ALT+S is pressed
#keyboard.wait('ctrl+alt+s')
login(email,password)
#print("CTRL+ALT+S detected. Moving forward...")

# Open a new tab
driver.execute_script("window.open('', '_blank');")

# Switch to the new tab
driver.switch_to.window(driver.window_handles[-1])
#filters=eyJyZWNlbnRfcG9zdHM6MCI6IntcIm5hbWVcIjpcInJlY2VudF9wb3N0c1wiLFwiYXJnc1wiOlwiXCJ9In0%3D

if(RECENT_POSTS):
    driver.get("https://www.facebook.com/search/posts/?q="+query+"&filters=eyJyZWNlbnRfcG9zdHM6MCI6IntcIm5hbWVcIjpcInJlY2VudF9wb3N0c1wiLFwiYXJnc1wiOlwiXCJ9In0%3D")
else:
    driver.get("https://www.facebook.com/search/posts/?q="+query)


# Find all elements matching the XPath
all_comments = []
current_index = 0
while NUMBER_OF_POSTS < MAX_NUMBER_OF_POSTS:

    comment_buttons = driver.find_elements(By.XPATH, '//div[@aria-label="Pridať komentár"]')
    btn = comment_buttons[current_index]
    #time.sleep(1)
    driver.execute_script("""
    var viewPortHeight = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
    var elementTop = arguments[0].getBoundingClientRect().top;
    window.scrollBy(0, elementTop - (viewPortHeight / 2));
    """, btn)
    time.sleep(3)
    try:
        btn.click()
    except Exception as e:
        #print("Najrelevantnejšie button not found or not clickable", e)
        current_index += 1 
        continue  
         
    time.sleep(3)

    try:
        komentare_kategorie = driver.find_element(By.XPATH, "//div[@class='x9f619 x1n2onr6 x1ja2u2z xt0psk2 xuxw1ft']")
        komentare_kategorie.click()
        time.sleep(3)  # Adjust delay as necessary
    except Exception as e:
        #print("Najrelevantnejšie button not found or not clickable", e)
        current_index += 1 
        continue  

    title_element = driver.find_element(By.XPATH, "//span[contains(text(), 'Príspevok používateľa')]")
    # Extract title
    title_text = title_element.text
    #//div[@class="x78zum5 xdt5ytf x1iyjqo2 x1n2onr6 x1jxyteu x1mfppf3 xqbnct6 xga75y6"]//div[@class="xdj266r x11i5rnm xat24cr x1mh8g0r x1vvkbs x126k92a"]
    # Extract description
    
    desc = driver.find_elements(By.XPATH, '//div[@class="x78zum5 xdt5ytf x1iyjqo2 x1n2onr6 x1jxyteu x1mfppf3 xqbnct6 xga75y6"]//div[@class="xdj266r x11i5rnm xat24cr x1mh8g0r x1vvkbs x126k92a"]')
    child_desc = []
    for element in desc:
        child_desc.extend(element.find_elements(By.XPATH, ".//div[@dir='auto']"))

    full_desc = ' '.join([div.text for div in child_desc])
    # Initialize formatted_comments for this iteration
    formatted_comments = {"TITLE": title_text, "DESCRIPTION":full_desc,"OPINIONS": []}

    # Click 'Všetky komentáre' (if necessary)
    try:
        vsetky_komentare_button = driver.find_element(By.XPATH, "//span[contains(text(), 'Všetky komentáre')]")
        vsetky_komentare_button.click()
        time.sleep(3)  # Adjust delay as necessary
    except Exception as e:
        #print("Všetky komentáre button not found or not clickable", e)
        close = driver.find_element(By.XPATH, "//div[@class='x1i10hfl x1ejq31n xd10rxx x1sy0etr x17r0tee x1ypdohk xe8uvvx xdj266r x11i5rnm xat24cr x1mh8g0r x16tdsg8 x1hl2dhg xggy1nq x87ps6o x1lku1pv x1a2a7pz x6s0dn4 x14yjl9h xudhj91 x18nykt9 xww2gxu x972fbf xcfux6l x1qhh985 xm0m39n x9f619 x78zum5 xl56j7k xexx8yu x4uap5 x18d9i69 xkhd6sd x1n2onr6 xc9qbxq x14qfxbe x1qhmfi1']")
        close.click()
        time.sleep(2)
        current_index += 1
        continue

    comments_dict = {}  # Initialize a dictionary for this set of comments
    comment_id = 1
    comment_containers = driver.find_elements(By.XPATH, "//div[@class='x1n2onr6']//div[@class='xdj266r x11i5rnm xat24cr x1mh8g0r x1vvkbs']")

    for container in comment_containers:
        child_divs = container.find_elements(By.XPATH, ".//div[@dir='auto']")
        full_comment = ' '.join([div.text for div in child_divs])
        
        
        # Add each comment to the comments_dict
        if(full_comment != ""):
            comments_dict[str(comment_id)] = full_comment
            comment_id += 1
            NUMBER_OF_COMMENTS += 1

    # Add the comments_dict to formatted_comments["OPINIONS"]
    formatted_comments["OPINIONS"].append(comments_dict)

    all_comments.append(formatted_comments)
    time.sleep(2)

    close_button = driver.find_element(By.XPATH,'//div[@aria-label="Zavrieť"]')
    close_button.click()
    NUMBER_OF_POSTS+=1
    
#output_directory = '../outputs'
#output_file = os.path.join(output_directory, 'facebook_output.json')
#
## Create the directory if it does not exist
#if not os.path.exists(output_directory):
#    os.makedirs(output_directory)
#
## Write all comments to the file
#try:
#    with open(output_file, 'w', encoding='utf-8') as file:
#        print(all_comments)
#        json.dump(all_comments, file, ensure_ascii=False, indent=4)
#except Exception as e:
#    print("Error writing to file:", e)

print(json.dumps(all_comments, ensure_ascii=False, indent=4))

#print("-NUMBER OF WANTED POSTS: " + str(MAX_NUMBER_OF_POSTS))
#print("-NUMBER OF EXTRACTED POSTS: " + str(NUMBER_OF_POSTS))
#print("-NUMBER OF EXTRACTED COMMENTS: " + str(NUMBER_OF_COMMENTS))