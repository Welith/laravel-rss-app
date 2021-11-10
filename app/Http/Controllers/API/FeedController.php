<?php

namespace App\Http\Controllers\API;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Repositories\Feed\FeedRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $request->validate([
            'title' => 'required',
            'link' => 'required',
            'description' => 'required',
            'publish_date' => 'required'
        ]);

        $this->feedRepository->create($request->all());

        return response()->json([
            'status' => 200,
            'message' => 'Feed Added Successfully!'
        ]);
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
        $request->validate([
            'title' => 'required',
            'link' => 'required',
            'description' => 'required',
            'publish_date' => 'required'
        ]);

        $this->feedRepository->update($id, $request->all());

        return response()->json([
            "status" => 200,
            "message" => 'Feed Updated Successfully!'
        ]);
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
