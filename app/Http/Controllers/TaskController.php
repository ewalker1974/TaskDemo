<?php

namespace App\Http\Controllers;

use App\Exceptions\BadArgumentException;
use App\Models\Task;
use App\Models\TasksChangelog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    private const DEFAULT_PAGE_SIZE = 10;

    private array $changedValues = [];

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->integer('page', 1);
        $size = $request->integer('size', self::DEFAULT_PAGE_SIZE);

        if (!$page) {
            $page = 1;
        }
        if (!$size) {
            $size = self::DEFAULT_PAGE_SIZE;
        }

        $pageData = Task::query()
            ->orderBy('id')
            ->paginate($size, ['*'], 'page', $page);

        return new JsonResponse([
            'page' => $page,
            'last_page' => $pageData->lastPage(),
            'per_page' => $size,
            'total' => $pageData->total(),
            'data' => $pageData->items(),
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws BadArgumentException
     */
    public function store(Request $request): JsonResponse
    {
        $errors = $this->validateStore($request->all());
        if (count($errors)) {
            throw new BadArgumentException('Input parameters are invalid', 0, $errors);
        }
        $task = new Task();
        $task = $this->setTask($request, $task);
        return new JsonResponse(['data' => $task->toArray()], Response::HTTP_CREATED);
    }

    /**
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        return new JsonResponse(['data' => $task->toArray()]);
    }

    /**
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     * @throws BadArgumentException
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $requestValues = $request->all();
        $errors = $this->validateUpdate($requestValues);
        if (count($errors)) {
            throw new BadArgumentException('Input parameters are invalid', 0, $errors);
        }
        DB::transaction( function () use ($task, $request, $requestValues) {
            $changes = $this->getChangedValues($task, $requestValues);
            $this->setTask($request, $task);
            if (count($changes) > 0) {
                $taskChangeLog = new TasksChangelog();
                $taskChangeLog->task_id = $task->id;
                $taskChangeLog->changes = json_encode($changes);
                $taskChangeLog->save();
            }
        });

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Task $task
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function taskChanges(Task $task, Request $request): LengthAwarePaginator
    {
        $page = $request->integer('page', 1);
        $size = $request->integer('size', self::DEFAULT_PAGE_SIZE);

        if (!$page) {
            $page = 1;
        }
        if (!$size) {
            $size = self::DEFAULT_PAGE_SIZE;
        }

        $pageData = TasksChangelog::query()
            ->where('task_id', $task->id)
            ->orderBy('id')
            ->paginate($size, ['*'], 'page', $page);

        return new JsonResponse([
            'page' => $page,
            'last_page' => $pageData->lastPage(),
            'per_page' => $size,
            'total' => $pageData->total(),
            'data' => $pageData->items(),
        ]);
    }

    /**
     * @param Task $task
     * @param array $values
     * @return array
     */
    private function getChangedValues(Task $task, array $values): array
    {
        if (count($this->changedValues) === 0) {

            if (isset($values['assigned_user_id'])) {
                $user = User::query()->where('uid', $values['assigned_user_id'])->first();
                if ($task->assigned_user_id != $user->id) {
                    $this->changedValues['assigned_user_id'] = [
                        'old' => $task->user->uid,
                        'new' => $values['assigned_user_id']
                    ];
                    $this->changedValues['user_id'] = $user->id;
                }
            }
            if (isset($values['status'])) {
                $this->changedValues['status'] = [
                    'old' => $task->status,
                    'new' => $values['status']
                ];
            }
            if (isset($values['due_to'])) {
                $this->changedValues['due_to'] = [
                    'old' => $task->due_to,
                    'new' => $values['due_to']
                ];
            }
            if (isset($values['name'])) {
                $this->changedValues['name'] = [
                    'old' => $task->name,
                    'new' => $values['name']
                ];
            }
        }
        return $this->changedValues;
    }

    /**
     * @param array $data
     * @return array
     */
    private function validateUpdate(array $data): array
    {
        $v = Validator::make(
            $data,
            [
                'due_to' => 'date',
                'status' => 'in:assigned,in progress,testing,done',
                'assigned_user_id' => 'exists:users,uid',
            ],
            [
                'due_to.date' => 'due_to should be date value',
                'status.in' => 'state should be one of: assigned, in progress, testing, done',
                'assigned_user_id.exists' => 'assigned_user_id should exists',
            ]
        );
        return $v->errors()->messages();
    }

    /**
     * @param array $data
     * @return array
     */
    private function validateStore(array $data): array
    {
        $v = Validator::make(
            $data,
            [
                'due_to' => 'required|date',
                'status' => 'required|in:assigned,in progress,testing,done',
                'name' => 'required',
                'assigned_user_id' => 'required|exists:users,uid',
            ],
            [
                'due_to.date' => 'due_to should be date value',
                'status.in' => 'state should be one of: assigned, in progress, testing, done',
                'assigned_user_id.exists' => 'assigned_user_id should exists',
                'required' => 'the value of field :attribute should be set',
            ]
        );
        return $v->errors()->messages();
    }

    /**
     * @param Request $request
     * @param Task $task
     * @return Task
     */
    private function setTask(Request $request, Task $task): Task
    {
        $name = $request->get('name');
        $dueTo = $request->get('due_to');
        $status = $request->get('status');
        $assignedUserUid = $request->get('assigned_user_id');
        $assignedUserId = null;
        if ($assignedUserUid) {
            $user = User::query()->where('uid', $assignedUserUid)->first();
            $assignedUserId = $user->id;
        }
        foreach (['name' => 'name', 'status' => 'status', 'due_to' => 'dueTo', 'assigned_user_id' => 'assignedUserId'] as $field => $value) {
            if (!empty(${$value})) {
                $task->{$field} = ${$value};
            }
        }
        $task->save();
        return $task;
    }
}
