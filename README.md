# Yrgopelag

In this task we were given the instructions to make a Hotel on an Island. We were also given instructions of how the booking should flow. Every hotel should've had three rooms ranging in price depending on the room. There was also a rating system which determined how many stars your hotel would have. How it worked was for however many extra tasks you completed you would gain a star.


# Magical Island Hotel

Our Magical hotel will leave you with unforgetable memories!

# Techniologies Used

PHP, HTML, CSS, SQLite and Composer(guzzle).

# Code review (made by Patricia Loayza Frykberg)
Nice hotel! Code is easy to read and to understand. Here are some things you could think about in future projects:

1.	Prices are hardcoded in PHP variables despite being stored in the database. That means that Code changes are required if there is any price updates. I recommend fetching prices from the database to ensure consistency and easier maintenance. (index.php – row 4-6)

2.	You could use section/article instead of div for a section in your code. e.g div class= ”section” instead of div class= ”rooms”. (index.php - row 10) 

3.	Security risk: 'verify' => false disables security checks when connecting to the Central Bank. This should be removed to ensure safe payment processing. (booking.php - row 44-46)

4.	For safety reasons you should always store sensitive information, ex. password, API-key etc, in an .env file and place it in your .gitignore. (booking.php – row 12)

5.	It’s good to get used to using the constant __DIR__ when requiring files. It's not always necessary, but important in bigger projects (calender.php – row 5)

6.	It’s good to use e.g rem instead of px for font-size, for accessibility and scalability. (bookingstyle.css – row 84, 94, 117)

7.	You have .container defined in both style.css and bookingstyle.css with different styling. This works now since booking.php doesn't include the header, but consider using unique class names (e.g .booking-container) to avoid conflicts if the styles are used together in the future. (style.css & bookingstyle.css)
