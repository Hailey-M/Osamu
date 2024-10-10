import sys
import json
import google.generativeai as genai

API_KEY = "Insert Your API Key here" 

def connect_and_query(prompt):
    try:
        genai.configure(api_key=API_KEY)
        model = genai.GenerativeModel()
        response = model.generate_content(prompt)
        return response.text
    except Exception as e:
        return str(e)

if __name__ == "__main__":
    try:
        input_data = json.loads(sys.stdin.read())
        
        if 'message' in input_data:
            prompt = input_data['message']
            if not prompt:
                print(json.dumps({'error': 'No message content provided'}))
                sys.exit(1)
            
            try:
                answer = connect_and_query(prompt)
                print(json.dumps({'response': answer}))
            except Exception as e:
                print(json.dumps({'error': f'Error during AI model interaction: {str(e)}'}))
                sys.exit(1)
        else:
            print(json.dumps({'error': 'Invalid input data'}))
            sys.exit(1)
    except Exception as e:
        print(json.dumps({'error': f'Error parsing input data: {str(e)}'}))
        sys.exit(1)
