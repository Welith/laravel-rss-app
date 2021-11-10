<?php

namespace App\Http\Controllers\API;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Repositories\Feed\FeedRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedController extends Controller
{
    private FeedRepository $feedRepository;

    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
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
            'source' => 'max:191',
            'source_url' => 'url',
            'description' => 'required',
            'publish_date' => 'required'
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status' => 400,
                'message' => $validator->errors()
            ]);
        } else {

            $this->feedRepository->create($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Feed Added Successfully!'
            ]);
        }
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $feeds = $this->feedRepository->paginate();

        return response()->json([
            'status' => 200,
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

        return response()->json([
            'status' => 200,
            'feed' => $feed
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function edit(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required|max:191',
            'link' => 'required|url',
            'source' => 'max:191',
            'source_url' => 'url',
            'description' => 'required',
            'publish_date' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => 400,
                'message' => $validator->errors()
            ]);
        } else {
            $this->feedRepository->update($id, $request->all());

            return response()->json([
                "status" => 200,
                "message" => 'Feed Updated Successfully!'
            ]);
        }
    }

    public function delete($id)
    {
        $this->feedRepository->delete($id);

        return response()->json([
            "status" => 200,
            "message" => 'Feed Deleted Successfully!'
        ]);
    }

}
