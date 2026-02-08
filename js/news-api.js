// News API Integration for Latest Announcements
// Focus: Business, Products, Export, and Product Improvement

const NEWS_API_KEY = '5060553216914b7b8b49bc49390b9425'; // Replace with your NewsAPI.org key
const NEWS_API_URL = 'https://newsapi.org/v2/everything';

// Keywords related to business, products, and export in the Philippines
const KEYWORDS = [
    'Philippines business',
    'Philippine exports',
    'MSME Philippines',
    'Philippine products',
    'Philippines manufacturing',
    'Philippine trade',
    'local products Philippines',
    'business development Philippines',
    'Philippine economy'
];

// Demo/Fallback news data
const DEMO_NEWS = [
    {
        title: "Philippine MSMEs Gain Access to New Export Markets Through Government Programs",
        description: "Local producers in the Philippines are expanding their reach to international markets with support from LGU initiatives. New trade agreements open doors for Philippine products...",
        url: "#",
        urlToImage: "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=2070&auto=format&fit=crop",
        publishedAt: new Date().toISOString()
    },
    {
        title: "Innovation in Philippine Manufacturing Boosts Product Quality and Competitiveness",
        description: "Filipino manufacturers are adopting modern technologies and quality improvement strategies. Training programs help local businesses meet international standards for export...",
        url: "#",
        urlToImage: "https://images.unsplash.com/photo-1556740758-90de374c12ad?q=80&w=2070&auto=format&fit=crop",
        publishedAt: new Date(Date.now() - 86400000).toISOString()
    },
    {
        title: "Philippine Government Launches Business Development Support for Local Producers",
        description: "Comprehensive programs provide packaging, branding, and market access support for Philippine MSMEs. Success stories show significant growth in both local and international sales...",
        url: "#",
        urlToImage: "https://images.unsplash.com/photo-1542744173-8e7e53415bb0?q=80&w=2070&auto=format&fit=crop",
        publishedAt: new Date(Date.now() - 172800000).toISOString()
    }
];

async function fetchBusinessNews() {
    try {
        // Check if API key is configured
        if (!NEWS_API_KEY || NEWS_API_KEY === 'YOUR_API_KEY_HERE') {
            console.log('Using demo news data. Configure NewsAPI key for live news.');
            displayNews(DEMO_NEWS);
            return;
        }

        // Build query with Philippines-focused keywords
        const query = KEYWORDS.join(' OR ');

        // Fetch more articles to filter Philippines-related news
        const url = `${NEWS_API_URL}?q=${encodeURIComponent(query)}&language=en&sortBy=publishedAt&pageSize=6&apiKey=${NEWS_API_KEY}`;

        const response = await fetch(url);
        const data = await response.json();

        if (data.status === 'ok' && data.articles && data.articles.length > 0) {
            displayNews(data.articles);
        } else {
            console.log('No live articles found, using demo data');
            displayNews(DEMO_NEWS);
        }
    } catch (error) {
        console.error('Error fetching news:', error);
        console.log('Using demo news data due to error');
        displayNews(DEMO_NEWS);
    }
}

function displayNews(articles) {
    const newsGrid = document.querySelector('.news-grid');

    if (!newsGrid) {
        console.error('News grid not found');
        return;
    }

    // Clear existing news
    newsGrid.innerHTML = '';

    // Display up to 3 articles
    articles.slice(0, 3).forEach(article => {
        const newsItem = createNewsItem(article);
        newsGrid.appendChild(newsItem);
    });

    console.log(`Displayed ${articles.length} news articles`);
}

function createNewsItem(article) {
    const newsItem = document.createElement('article');
    newsItem.className = 'news-item';

    // Format date
    const date = new Date(article.publishedAt);
    const formattedDate = date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });

    // Get image or use placeholder
    const imageUrl = article.urlToImage || 'https://images.unsplash.com/photo-1541873676947-d6b2c9b99264?q=80&w=2070&auto=format&fit=crop';

    // Truncate description
    const description = article.description
        ? (article.description.length > 120
            ? article.description.substring(0, 120) + '...'
            : article.description)
        : 'Read more about this business and export development story...';

    newsItem.innerHTML = `
        <div class="news-thumb">
            <img src="${imageUrl}" alt="${article.title}" onerror="this.src='https://images.unsplash.com/photo-1541873676947-d6b2c9b99264?q=80&w=2070&auto=format&fit=crop'">
        </div>
        <div class="news-content">
            <span class="date">${formattedDate}</span>
            <h3>${article.title}</h3>
            <p>${description}</p>
            <a href="${article.url}" target="_blank" rel="noopener noreferrer">Read More</a>
        </div>
    `;

    return newsItem;
}

// Initialize news on page load
document.addEventListener('DOMContentLoaded', () => {
    console.log('Initializing news feed...');
    fetchBusinessNews();
});
