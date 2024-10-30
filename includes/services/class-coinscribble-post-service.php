<?php
class Coinscribble_Integration_Post_Service {
	private static $instance = null;

	private function __construct() {
	}

	public static function get_instance() {
		if ( self::$instance == null ) {
			self::$instance = new Coinscribble_Integration_Post_Service();
		}

		return self::$instance;
	}

    protected function wp_kses($content)
    {
        return wp_kses($content, array(
            'br' => array(),
            'strong' => array(),
            'bold' => array(),
            'b' => array(),
            'i' => array(),
            'ol' => array(),
            'ul' => array(),
            'li' => array(),
            'a' => array(
                'href' => array(),
                'title' => array(),
                'target' => array(),
                'rel' => array(),
            ),
            'img' => array(
                'src' => array(),
                'alt' => array(),
            ),
            'video' => array(),
            'h1' => array(),
            'h2' => array(),
            'h3' => array(),
            'h4' => array(),
            'h5' => array(),
            'h6' => array(),
            'p' => array(
                'class' => array(),
            ),
            'blockquote' => array(),
            'u' => array(),
            'del' => array(),
            'span' => array(),
            'iframe' => array(
                'src' => array()
            ),
        ));
    }

	public function create_post(string $title, string $content, string $post_type, $featured_image) {

		$category = get_category( Coinscribble_Integration_Categories_Configs::get_settings_for_category($post_type)['id']);
		if (is_wp_error($category) || $category == null) {
			Coinscribble_Integration_Error_Notification::add_error(Coinstribble_Integration_Notice_Types::CATEGORY_ERROR, __('Category not found, please set up categorization in coinscribble setup page!', 'coinscribble-integration'));

			return ['success' => false, 'message' => 'Category not found!'];
		}

		$user_service = Coinscribble_Integration_User_Service::get_instance();
		$author_id = $user_service->create_if_not_exists_user();

		if (is_wp_error($author_id) || !$author_id) {
			return ['success' => false, 'message' => 'Cant create  new user '. wp_json_encode($author_id->errors ?? ''). '!'];
		}

		$content = $this->regenerate_post_content($content);

		$content_html = $this->wp_kses($content['html']);

		$my_post = array(
			'post_title' => $title,
			'post_content' => $content_html,
			'post_status' => 'publish',
			'post_author' => $author_id,
			'post_category' => [$category->term_id]
		);
		$post_id = wp_insert_post($my_post, true);

		if (is_wp_error($post_id) || $post_id === 0) {
			return ['success' => false, 'message' => 'Cant create post : '. wp_json_encode($post_id->errors ?? $my_post)];
		}

        $this->set_attachments_to_post($content['attachments_id'], $post_id);
		$this->set_post_thumbnail($post_id, $featured_image, $content['attachments_id']);

        update_post_meta($post_id, Coinscribble_Integration_Meta_Keys::IS_Coinscribble_POST, 1);
		update_post_meta($post_id, Coinscribble_Integration_Meta_Keys::Coinscribble_CATEGORY, $post_type);

		return ['success' => true, 'link' => get_the_guid($post_id), 'post_id' => $post_id];
	}

	public function set_post_thumbnail($post_id, $attachment_url, $attachments) {
		if (!empty($attachment_url)) {
			$data = $this->get_attachment_data($attachment_url);
			$this->set_attachments_to_post([$data['attachment_id']], $post_id);
			return set_post_thumbnail($post_id, $data['attachment_id']);
		} elseif (!empty($attachments)) {
			return set_post_thumbnail($post_id, $attachments[0]);
		}

		return false;
	}

    protected function set_attachments_to_post(array $attachments, int $post_id)
    {
        foreach ($attachments as $attachment_id) {
            wp_update_post( array(
                'ID'            => $attachment_id,
                'post_parent'   => $post_id,
            ), true );
        }
    }

	protected function download_file($url)
	{
		$http = new WP_Http();
		$response = $http->request($url);
		if (is_wp_error($response) || $response['response']['code'] !== 200) {
			return false;
		}
		$upload = wp_upload_bits(basename($url), null, $response['body']);
		if (!empty($upload['error'])) {
			return false;
		}
		return $upload;
	}

	protected function get_attachment_data($src)
	{
		$attachment_id = null;

		$upload = $this->download_file($src);
		if ($upload && $upload['url']) {
			$src = $upload['url'];
		}
		if ($upload && $upload['file']) {
			$attachment_id = $this->set_attachment($upload['file']);
		}
		return [
			'url' => $src,
			'attachment_id' => $attachment_id
		];
	}

	protected function set_attachment($file)
	{
		$filename = basename($file);
		$mime_type = wp_check_filetype($filename, null);
		$attachment = array(
			'post_mime_type' => $mime_type['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
		);
		$attachment_id = wp_insert_attachment($attachment, $file);
		if (!is_wp_error($attachment_id)) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$attachment_data = wp_generate_attachment_metadata($attachment_id, $file);
			wp_update_attachment_metadata($attachment_id, $attachment_data);
		}
		return $attachment_id;
	}

    protected function regenerate_post_content($html)
    {
        $doc = new DOMDocument();
        $utf8Meta = '<?xml encoding="utf-8" ?>';
        $doc->loadHTML($utf8Meta . $html);
        $tags = $doc->getElementsByTagName('img');
        $attachments_id = [];

        foreach ($tags as $tag) {
            $img = $tag->getAttribute('src');
            $attachment_data = $this->get_attachment_data($img);
            $src = $attachment_data['url'];
            $attachment_id = $attachment_data['attachment_id'];
            if ($src && $attachment_id) {
                $src_set = wp_get_attachment_image_srcset($attachment_id);
                if ($src_set) {
                    $tag->setAttribute('src_set', $src_set);
                }
                $src_large = wp_get_attachment_image_url($attachment_id, 'large');
                $tag->setAttribute('src', $src_large);
                $attachments_id[] = $attachment_id;
            }
        }
        $bodyContent = '';
        foreach ($doc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $bodyContent .= $doc->saveHTML($node);
        }

        return ['html' => $bodyContent, 'attachments_id' => $attachments_id];
    }

    public function update_post(int $id, string $title, string $content, string $post_type, $featured_image)
    {
        $category = get_category( Coinscribble_Integration_Categories_Configs::get_settings_for_category($post_type)['id']);
        if (is_wp_error($category) || $category == null) {
            Coinscribble_Integration_Error_Notification::add_error(Coinstribble_Integration_Notice_Types::CATEGORY_ERROR, __('Category not found, please set up categorization in coinscribble setup page!', 'coinscribble-integration'));

            return ['success' => false, 'message' => 'Category not found!'];
        }

        $this->remove_attachments($id);

        $content = $this->regenerate_post_content($content);

        $this->set_attachments_to_post($content['attachments_id'], $id);

        $content_html = $this->wp_kses($content['html']);
        update_post_meta($id, Coinscribble_Integration_Meta_Keys::Coinscribble_CATEGORY, $post_type);

        $my_post = array(
            'ID' => $id,
            'post_title' => $title,
            'post_content' => $content_html,
            'post_status' => 'publish',
            'post_category' => [$category->term_id]
        );
        $post_id = wp_update_post($my_post, true);

	    $this->set_post_thumbnail($post_id, $featured_image, $content['attachments_id']);


        if (is_wp_error($post_id) || $post_id === 0) {
            return ['success' => false, 'message' => 'Cant update post : '. wp_json_encode($post_id->errors ?? $my_post)];
        }

	    return ['success' => true];
    }

    private function remove_attachments(int $id)
    {
        $attachments = get_posts( array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
            'post_parent' => $id,
        ) );

        if ( $attachments ) {
            foreach ( $attachments as $attachment ) {
                wp_delete_attachment($attachment->ID, true);
            }
        }
    }

	public function getCoinscribbleCategory(int $post_id) {
		$slug = get_post_meta($post_id, Coinscribble_Integration_Meta_Keys::Coinscribble_CATEGORY, true);
		if ( empty($slug) ) {
			$slug = '';
			$categories = wp_get_post_categories($post_id);
			$cat_settings = Coinscribble_Integration_Categories_Configs::get_all_settings();
			foreach ($categories as $category) {
				foreach ($cat_settings as $key => $cat_setting) {
					if ($cat_setting['id'] && $cat_setting['id'] == $category){
						return $key;
					}
				}
			}
		}
		return $slug;
	}
}
