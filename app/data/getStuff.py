import urllib2, json, sys, time
portal2 = ["http://www.portal2sounds.com/", 2654]
portal2dlc1 = [portal2url.replace("www", "dlc"), 207]
portal2dlc2 = [portal2url.replace("www", "dlc2"), 81]
portal2music = [portal2url.replace("www", "p2music"), 244]
portal1 = [portal2url.replace("www", "p1"), 406]
portal1music = [portal2url.replace("www", "p1music"), 13]
tf2 = ["http://www.tf2sounds.com/", 2681]
tf2music = [tf2url.replace("www", "music"), 18]
ua_chrome = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.4 (KHTML, ' \
            'like Gecko) Chrome/22.0.1229.79 Safari/537.4'


def getSoundInfo(url, id, file):
    url = url + "list.php?id=" + id
    request = urllib2.Request(url)
    request.add_header('User-Agent', ua_chrome)
    opener = urllib2.build_opener()
    data = json.loads(opener.open(request).read())
    if len(data) > 3:
        file.write(" {}: ".format(id) + json.dumps(data[3]) + ",\n")
        print "Done " + id
    else:
        print "Skipping " + id

def getList(url, filename, start=1):
    maxid = url[1]
    url = url[0]
    try:
        with open(filename) as a:
            exists = True
    except IOError:
        exists = False
    with open(filename, "a") as f:
        if not exists:
            f.write("[\n")
        for i in range(start, maxid+1):
            getSoundInfo(url, str(i), f)
            time.sleep(0.5)
        f.write("]")
    print "Done ids 1-" + str(maxid)
