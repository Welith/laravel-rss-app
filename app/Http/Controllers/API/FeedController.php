<?php

namespace App\Http\Controllers\API;

use App\Constants\RequestConstants;
use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Repositories\Feed\FeedRepository;
use App\RequestManagers\FeedRequestManager;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedController extends Controller
{
    private FeedRepository $feedRepository;

    private FeedRequestManager $feedRequestManager;

    public function __construct(FeedRepository $feedRepository, FeedRequestManager $feedRequestManager)
    {
        $this->feedRepository = $feedRepository;
        $this->feedRequestManager = $feedRequestManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GeneralException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:191',
            'link' => 'required|url',
            'source' => 'nullable|max:191',
            'source_url' => 'nullable|url',
            'description' => 'required',
            'publish_date' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => RequestConstants::STATUS_CODES['validation'],
                'message' => $validator->errors()
            ]);
        }

        [$status, $message] = $this->feedRepository->create($request->all());

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->query->all();

        $feeds = $this->feedRepository->getFiltered($filters);

        return response()->json([
            'status' => RequestConstants::STATUS_CODES['success'],
            'feeds' => $feeds
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $feed = $this->feedRepository->find($id);

        if (!$feed) {

            return response()->json([
                'status' => RequestConstants::STATUS_CODES['not_found'],
                'message' => RequestConstants::RESPONSES['not_found']
            ]);
        }

        return response()->json([
            'status' => RequestConstants::STATUS_CODES['success'],
            'feed' => $feed
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GeneralException
     * @throws GuzzleException
     * @throws \JsonException
     */
    public function fetchFromGolang(Request $request)
    {
        $feeds = $this->feedRequestManager->getFeeds($request->all());

        if (empty($feeds)) {

            return response()->json([
                'status' => RequestConstants::STATUS_CODES['not_found'],
                'message' => RequestConstants::RESPONSES['not_found']
            ]);
        }

        foreach ($feeds as $feed) {

            $validator = Validator::make($feed, [
                'title' => 'required|max:191',
                'link' => 'required|url',
                'source' => 'nullable|max:191',
                'source_url' => 'nullable|url',
                'description' => 'required',
                'publish_date' => 'required'
            ]);

            $duplicate = $this->feedRepository->checkForDuplicates($feed);

            if (!$validator->fails() && !$duplicate) {

                $this->feedRepository->create($feed);
            }
        }

        return response()->json([
            'status' => RequestConstants::STATUS_CODES['success'],
            'message' => RequestConstants::RESPONSES['created']
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws GeneralException
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:191',
            'link' => 'required|url',
            'source' => 'nullable|max:191',
            'source_url' => 'nullable|url',
            'description' => 'required',
            'publish_date' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => RequestConstants::STATUS_CODES['validation'],
                'message' => $validator->errors()
            ]);
        }

        [$status, $message] = $this->feedRepository->update($id, $request->all());

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function delete($id): JsonResponse
    {
        [$status, $message] = $this->feedRepository->delete($id);

        return response()->json([
            "status" => $status,
            "message" => $message
        ]);
    }

}
