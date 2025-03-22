<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #ff758c, #ff7eb3);
            font-family: Arial, sans-serif;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        h2 {
            margin-bottom: 1rem;
            color: #333;
        }
        input {
            width: 94%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #ff5a8a;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #e94e7b;
        }
        .switch {
            margin-top: 10px;
            color: #555;
        }
        .switch a {
            color: #ff5a8a;
            text-decoration: none;
            font-weight: bold;
        }
        .error-message {
    color: red;
    margin: 10px 0;
    padding: 10px;
    background: #ffe6e6;
    border-radius: 5px;
}
    </style>