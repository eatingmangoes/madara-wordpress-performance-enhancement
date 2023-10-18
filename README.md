# Manga Chapter Reading Cache Generator

Due to the nature of how madara loads each chapter (it iteratively generates the php code for each images line by line), it uses a lot of cpu. This script aims to not use that computing everytime the page is generated. Before you continue, I'd to like to mention the limitation of this method, this is a very good way of loading the site if you use a low end server and have mid to higher mid range traffic, because when you hit hundreds of thousands of users at the same moment, the limitation would ultimately become the ssd speed (although the traffic should be be really high if using an ssd), at which point it is more recommended to just get a better vps with a faster cpu than this method, since its easier to scale if the processing is done on the cpu.

Also it is nice to mention, that this method is especially useful when you add more customisation, like in my case, i tend to add mid ads when generating the page and also add fixed height and width of the image in the img tag (for some reason this doesn't exist already in madara), because it would reduce the cumulative layout shift, giving better score in lighthouse.

## Usage

1. choose the appropriate functions you want in your content-reading-list.php

2. Copy and paste the PHP code into Madara --> madara-core --> reading-content --> content-reading-list.php (Use the Theme File Editor in wordpress).

3. The script will automatically check if a cached version of the chapter content exists. If it does, it will load the cached content, saving server resources and improving loading speed. If not, it will generate the cache and store it for subsequent requests.

4. The cached content is stored in text files in the `/var/www/wordpress/sitecached/` directory (hence create a directory at your wordpress root directory called sitecached, if your directory of wordpress is not /var/www/wordpress/ then please change that in line 116, with the directory of your sitecached folder). Each chapter content is cached in a separate file named by its `chapter_id`. So in case of reuploads, just delete the chapter_id.txt file, and it would regenerate the txt file again.

5. The generated cache content is displayed in place of the chapter's content. It's intended to be used on the chapter reading page of your manga website.

6. The cache generation occurs when a user visits a chapter for the first time. Subsequent visitors to the same chapter will benefit from the cached content.

## File Structure

- `cachecreator()`: The main function responsible for generating and caching chapter content.

- `if (file_exists($filename))`: Checks if a cached version of the chapter exists and loads it if it does.

- `else`: If no cached version exists, it calls `cachecreator()` to generate the cache.
