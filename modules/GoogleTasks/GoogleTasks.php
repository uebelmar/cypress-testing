<?php
namespace SpiceCRM\modules\GoogleTasks;

use Exception;
use SpiceCRM\modules\GoogleOAuth;//todo-uebelmar class does not exist
use SpiceCRM\data\SugarBean;

class GoogleTasks
{
    //const CLIENT_ID = "231412431944-gf2mtlmpt4rtdvmi3bs5dtvm8rvb7voh.apps.googleusercontent.com";
    //const CLIENT_SECRET = "GIIHR2R51h40R9pPb2dQmmhb";

    public $tasklistId;

    /**
     * GoogleTasks constructor.
     */
    public function __construct() {
        $this->tasklistId = '@default'; // todo add some configuration for this
    }

    /**
     * createTasks
     *
     * Adds or updates a Task from Google Tasks
     *
     * @param SugarBean $bean
     * @return Exception|GoogleTask
     */
    public function createTask(SugarBean $bean) {

        $task = $bean->toTask();

        try {
            if ($bean->external_id) {
                $result = $this->updateRequest($task);
            } else {
                $result = $this->insertRequest($task);
            }

            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * removeTask
     *
     * Removes a Task from Google Tasks
     *
     * @param $taskId
     * @return Exception
     * @throws Exception
     */
    public function removeTask($taskId) {
        if ($taskId == null) {
            throw new Exception('Missing Task ID.');
        }

        try {
            $this->deleteRequest($taskId);
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * cURL Requests
     */

    /**
     * insertRequest
     *
     * Insert request for Google Tasks using cURL
     *
     * @param GoogleTask $task
     * @return GoogleTask
     * @throws Exception
     */
    private function insertRequest(GoogleTask $task) {
        $apiUrl  = 'https://www.googleapis.com/tasks/v1/lists/' . $this->tasklistId . '/tasks';

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_POST           => 1,
            CURLOPT_POSTFIELDS     => $task->serialize(),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $_SESSION['google_oauth']['access_token'],
            ],
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);

        if (!$result) {
            throw new Exception('Cannot insert a new Google Tasks Task');
        }

        if (isset($result->error)) {
            throw new Exception($result->error->code . ': ' . $result->error->message);
        }

        $event = new GoogleTask((array) $result);

        return $event;
    }

    /**
     * updateRequest
     *
     * Update request for Google Tasks using cURL
     *
     * @param GoogleTask $task
     * @return GoogleTask
     * @throws Exception
     */
    private function updateRequest(GoogleTask $task) {
        $apiUrl  = 'https://www.googleapis.com/tasks/v1/lists/' . $this->tasklistId . '/tasks/';
        $apiUrl .= $task->id;

        $payload = $task->serialize();

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $_SESSION['google_oauth']['access_token'],
            ],
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);

        if (!$result) {
            throw new Exception('Cannot update a new Google Tasks Task');
        }

        if (isset($result->error)) {
            throw new Exception($result->error->code . ': ' . $result->error->message);
        }

        $event = new GoogleTask((array) $result);

        return $event;
    }

    /**
     * deleteRequest
     *
     * Delete request for Google Tasks using cURL
     *
     * @param $taskId
     */
    private function deleteRequest($taskId) {
        $apiUrl  = 'https://www.googleapis.com/tasks/v1/lists/' . $this->tasklistId . '/tasks/';
        $apiUrl .= $taskId;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => $apiUrl,
            CURLOPT_CUSTOMREQUEST  => 'DELETE',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $_SESSION['google_oauth']['access_token'],
            ],
        ]);

        $result = json_decode(curl_exec($curl));

        curl_close($curl);
    }
}
