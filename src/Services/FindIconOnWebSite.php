<?php

    namespace NeoxDashBoard\NeoxDashBoardBundle\Services;

    use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
    use Symfony\Contracts\HttpClient\HttpClientInterface;
    use DOMDocument;
    use DOMXPath;
    
    class FindIconOnWebSite
    {

        public function __construct(readonly private HttpClientInterface $httpClient){}

        /**
         * @throws TransportExceptionInterface
         */
        public function getFaviconUrl(string $url): ?string
        {
            $url = $this->extractDomain($url)["host"];

            // Add "https://" by default if the URL does not contain a scheme
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = 'https://' . ltrim($url, '/');
            }

            // Check if the URL is accessible with a HEAD request
            try {
                $response = $this->httpClient->request('HEAD', $url, [
                    'timeout' => 3, 'verify_peer' => true, 'verify_host' => true, 'max_redirects' => 5,'http_version' => '1.1', // Optional: Force HTTP/1.1
                ]);

                if ($response->getStatusCode() !== 200) {
                    return "500 : $url (HTTP " . $response->getStatusCode() . ")";
                }

            } catch (\Exception $e) {
                return "500 : $url (" . $e->getMessage() . ")";
            }

            // Get the content of the HTML page
            $html = @file_get_contents($url);
            if ($html === false) {
                return "500 : $url";
            }

            // Use DOMDocument to parse HTML and search for favicons
            $doc = new DOMDocument();
            @$doc->loadHTML($html);

            $xpath    = new DOMXPath($doc);
            $linkTags = $xpath->query("//link[contains(@rel, 'icon')]");

            // Check for the presence of an icon
            if ($linkTags->length > 0) {
                $faviconUrl = $linkTags
                    ->item(0)
                    ->getAttribute('href')
                ;

                // Normalize the URL if it is relative
                if (!str_starts_with($faviconUrl, 'http')) {
                    $parsedUrl = parse_url($url);

                    // Get the current directory path of the URL if it exists
                    $basePath = isset($parsedUrl[ 'path' ]) ? rtrim(dirname($parsedUrl[ 'path' ]), '/') : '';

                    // If the URL starts with "/", it is absolute with respect to the site root
                    $faviconUrl = (str_starts_with($faviconUrl, '/')) ? $parsedUrl[ 'scheme' ] . '://' . $parsedUrl[ 'host' ] . $faviconUrl : $parsedUrl[ 'scheme' ] . '://' . $parsedUrl[ 'host' ] . $basePath . '/' . ltrim($faviconUrl, '/');
                }

                return $faviconUrl;
            }
            return "500";
        }

        /**
         * @throws \Exception
         */
        public function extractDomain($url) {

            if (!preg_match('/^(https?|ftp):\/\//', $url)) {
                $url = 'http://' . $url; // Ajoute un schéma par défaut
            }

            // Parse URL and retrieve host
            $parsedUrl = parse_url($url);
            if ($parsedUrl === false) {
                return [
                    throw new \Exception("Invalid URL: $url"), // TODO: throw
                ];
            }
            $domain = isset($parsedUrl['host']) ? preg_replace('/^www\./', '', $parsedUrl['host']) : ($_SERVER['HTTP_HOST'] ?? 'localhost');

            // Initialize default output array
            $o = [
                "domain" => $domain,
                "host" => $parsedUrl['host'] ?? 'localhost',
            ];

            return $o;
        }
    }