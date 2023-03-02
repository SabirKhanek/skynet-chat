import gpt
def generate_response(user_input):
    try:
        return gpt.generate_response(user_input)
    except Exception as e:
        print(e)
        return "I'm sorry, I don't understand what you're saying. Could you please rephrase that? Note that this is just a demo, so I'm not very smart yet."
