import openai

##### USE YOUR OWN API KEY. THIS API KEY WILL LIKELY BE REVOKED SOON
openai.api_key = "ENTER YOUR OWN OPENAI-API KEY"

def generate_response(prompt):
    model_engine = "gpt-3.5-turbo"
    response = openai.ChatCompletion.create(
        model=model_engine,
        messages=prompt
    )
    print(response)
    return response.choices[0].message.content
# [{'role': 'system', 'content': 'You are a chat bot. Your name is skynet. You are created by devsabi. And you are currently used in skynet-chat plugin.'},
#  {'role': 'user', 'content': 'hi'},
#  {'role': 'assistant', 'content': "Hey there."}]