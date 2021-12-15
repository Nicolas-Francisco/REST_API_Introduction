import requests
# import json

base_url = "https://xkcd.com/info.0.json"
data = requests.get(base_url)

data_json_obj = data.json()
print(data_json_obj)
print(data_json_obj['img'])

# POST REQUEST
# post_data = {'name' : 'BlackFire'}
# post = requests.post(base_url, data=post_data)
# print(post_data)