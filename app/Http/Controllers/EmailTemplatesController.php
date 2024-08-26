<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplates;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemplateEmail;

class EmailTemplatesController extends Controller
{
    public function manageEmails()
    {
        $data['statuses'] = OrderStatus::all();
        $data['templates'] = EmailTemplates::with('for_status')->get();
        return view('admin.manage-emails', $data);
    }

    public function addEmailTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status_id' => 'required|integer',
            'subject' => 'required|string',
            'content' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $email = new EmailTemplates();
            $email->name = $request->input('name');
            $email->status_id = $request->input('status_id');
            $email->subject = $request->input('subject');
            $email->content = $request->input('content');
            $email->save();
            DB::commit();

            // Send test email
            $recipient = 'your_test_email@example.com'; // Replace with your test email address
            Mail::to($recipient)->send(new TemplateEmail($email->subject, $email->content));

            // Prepare response data
            $response = [
                'status' => 200,
                'message' => 'Email template added and test email sent successfully.',
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            $response = [
                'status' => 404,
                'message' => 'Something went wrong. ' . $e->getMessage(),
            ];
        }

        return response()->json($response);
    }

    public function updateStatus(Request $request)
    {
        $email = EmailTemplates::find($request->id);
        if ($email) {
            $email->status = isset($request->status) && $request->status == 'true' ? 1 : 0;
            $email->save();

            $response = [
                'status' => 200,
                'message' => 'Status updated successfully.',
            ];
        } else {
            $response = [
                'status' => 404,
                'message' => 'Something went wrong.',
            ];
        }

        return response()->json($response);
    }

    public function deleteEmail($id)
    {
        try {
            $email = EmailTemplates::find($id);

            if (!$email) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Email template not found.'
                ]);
            }

            $email->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Email template deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete email template. Error: ' . $e->getMessage()
            ]);
        }
    }

    public function sendTemplateEmail($templateId, $recipientEmail)
    {
        $template = EmailTemplates::find($templateId);

        if ($template) {
            // Ensure $content is a string
            $content = is_array($template->content) ? implode(' ', $template->content) : $template->content;

            // Prepare data for the email template
            $data = [
                'title' => $template->subject, // Assuming title is the subject
                'content' => $content, // Ensure this is a string
            ];

            // Send the email
            Mail::to($recipientEmail)->send(new TemplateEmail($template->subject, $data));

            return response()->json(['status' => 'Email sent successfully!']);
        } else {
            return response()->json(['status' => 'Template not found.'], 404);
        }
    }

    public function getTemplateData($id)
    {
        $template = EmailTemplates::find($id);

        if ($template) {
            return response()->json(['status' => 200, 'template' => $template]);
        } else {
            return response()->json(['status' => 404, 'message' => 'Template not found']);
        }
    }

    public function updateTemplate(Request $request, $id)
    {
        // Find the template by ID
        $template = EmailTemplates::findOrFail($id);
    
        // Update the template fields
        $template->name = $request->input('template_name');
        $template->subject = $request->input('subject');
        $template->status_id = $request->input('status_id');
        $template->content = $request->input('content');
    
        // Save the template
        $template->save();
    
        return response()->json([
            'status' => 200,
            'message' => 'Template updated successfully',
        ]);
    }



}
