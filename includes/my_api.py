import sys
import json
from urllib.parse import urlencode
from bs4 import BeautifulSoup
import requests
import time
from random import randint

def get_auth_page(name):
    # uname = urlencode(name)
    uname = name
    url = "https://scholar.google.com/"
    url_search = url + "/citations?mauthors=" + uname + "&hl=en&view_op=search_authors"
    req = requests.get(url_search)
    page = BeautifulSoup(req.content)
    names = page.select("#gs_ccl h3.gsc_1usr_name a")

    if len(names) >= 1:
        auth_url = url + names[0].get('href')
        return auth_url

    return False


def get_auth_papers_json(name):
    auth_page = get_auth_page(name)
    auth_page += "&cstart=0&pagesize=200"
    req = requests.get(auth_page)
    page = BeautifulSoup(req.content)
    rows = page.select('.gsc_a_tr')
    result = []
    for item in rows:
        title = item.select('.gsc_a_t a')
        title = title[0]
        citation = item.select('.gsc_a_c a')
        citation = citation[0]
        year = item.select('.gsc_a_y .gsc_a_h')
        year = year[0]
        result.append(dict(
            title = title.text, citation = citation.text, year = year.text
        ))
    return result

def get_auth_papers(name):
    count = 0
    auth_page = get_auth_page(name)
    # auth_page = get_auth_page(name)
    auth_page += "&cstart=0&pagesize=200"
    req = requests.get(auth_page)
    page = BeautifulSoup(req.content)
    rows = page.select('.gsc_a_tr')
    result = ""
    for item in rows:
        title = item.select('.gsc_a_t a')[0]
        more_info_url = item.select('.gsc_a_t a')[0].get('href')
        num_citations = item.select('.gsc_a_c a')[0]
        url_citations = item.select('.gsc_a_c a')[0].get('href')
        year = item.select('.gsc_a_y .gsc_a_h')[0]
        # req = requests.get(more_info_url)
        # page = BeautifulSoup(req.content)
        # excerpt = page.select('div#gsc_descr')[0]
        # if page.select('#gsc_title_gg .gsc_title_ggi a')[0].get('href') :
        #     url_pdf = page.select('#gsc_title_gg .gsc_title_ggi a')[0].get('href')
        # else:
        #     url_pdf = ''
        # if page.select('div#gsc_title a')[0].get('href'): url = page.select('div#gsc_title a')[0].get('href')
        # else: url = '';
        result += "{}|{}|{}|{}\n".format(title.text, num_citations.text,  year.text, url_citations)
        count += 1
    return result

def get_author_publc(name):
    auth_url = get_auth_page(name)
    auth_url += "&cstart=0&pagesize=200"
    req = requests.get(auth_url)
    page_content = BeautifulSoup(req.content)
    rows = page_content.select("#gsc_a_t #gsc_a_b .gsc_a_tr")

    # 1st: get rows, get(title, num_citations, url_citations, year). Then go to title.url to obtain more info
    title, more_info_url, num_citations, url_citations, year, results, count = "","",0,"",0,"", 0
    for index, row in enumerate(rows):
        title = row.select('.gsc_a_t a')
        title = title[0]
        citation = row.select('.gsc_a_c a')
        citation = citation[0]
        year = row.select('.gsc_a_y .gsc_a_h')
        year = year[0]
        results += "{}\n".format(title.txt)
        # result += "{}|{}|{}\n".format(title.text, citation.text, year.text)
        count += 1
    print(count)
    return results
    # 2nd: get (url, url_pdf, excerpt, optional[journal]


def main():
    # variable = get_auth_papers('Rizwan Jameel Qureshi')

    auth_name = sys.argv[1]
    variable = get_auth_papers(auth_name)
    print(variable, end='')
    # print('''{}\n WhateverMeeen'''.format(auth_name), end='')

    # sanitized = json.dumps(variable)
    # print(sanitized)

    # ************* test ****************
    # str = "<a id='w' href='google.com'>whatever</a>"
    # page = BeautifulSoup(str)
    # if page.select('#w')[0].get('href'): print(page.select('#w')[0].get('href'))
    # else: print('no')

    # ********* test **************
    # n = 0
    # while(n < 5):
    #     a = randint(1, 4)
    #     print("duration ", a)
    #     time.sleep(a)
    #     print("N is ", n)
    #     n+=1

if __name__ == "__main__": main()