<?php
class Coinscribble_Integration_Post_Controller extends WP_REST_Controller {


	public function create_item( $request ) {
		$response = new WP_REST_Response();
		try {

			$result = Coinscribble_Integration_Post_Service::get_instance()->create_post( $request->get_param( 'title' ), $request->get_param( 'content' ), $request->get_param( 'post_type' ), $request->get_param('featured_image') );

			if (!$result['success']) {
				$data = [
					'code' => 'post_creating_error',
					'message' => $result['message'],
					'data' => [
						'status' => 400,
					]
				];
				$response->set_status(400);
			} else {
				$data = [
					'code' => 'success',
					'message' => 'Created',
					'data' => [
						'status' => 200,
						'params' => [
							'link' =>  !empty(trim($result['link'])) ? $result['link'] : true,
							'post_id' => $result['post_id']
						]
					]
				];
				$response->set_status(200);
			}

		} catch (Throwable $exception) {
			$data = [
				'code' => 'fatal_error',
				'message' => $exception->getMessage(),
				'data' => [
					'status' => 500,
				]
			];
			$response->set_status(500);
		}

		$response->set_data($data);
		return $response;
	}

    public function update_item($request)
    {
        $response = new WP_REST_Response();
        try {

            $result = Coinscribble_Integration_Post_Service::get_instance()->update_post($request->get_param( 'post_id' ), $request->get_param( 'title' ), $request->get_param( 'content' ), $request->get_param( 'post_type' ), $request->get_param('featured_image')  );

            if (!$result['success']) {
                $data = [
                    'code' => 'post_updating_error',
                    'message' => $result['message'],
                    'data' => [
                        'status' => 400,
                    ]
                ];
                $response->set_status(400);
            } else {
                $data = [
                    'code' => 'success',
                    'message' => 'Updated',
                    'data' => [
                        'status' => 200,
                        'params' => [
                            'link' =>  !empty(trim($result['link'])) ? $result['link'] : true
                        ]
                    ]
                ];
                $response->set_status(200);
            }

        } catch (Throwable $exception) {
            $data = [
                'code' => 'fatal_error',
                'message' => $exception->getMessage(),
                'data' => [
                    'status' => 500,
                ]
            ];
            $response->set_status(500);
        }

        $response->set_data($data);
        return $response;
    }

}
