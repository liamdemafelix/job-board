<x-mail::message>
# Pending Review

A new job post is pending review.

<x-mail::button :url="config('app.frontend_url') . '/job/' . $jobPost->id">
View Post
</x-mail::button>

You may opt to approve the post or mark it as spam. If you choose to mark it as spam, it will remain hidden from the public and tagged accordingly.

If you approve the post, it will be visible to everyone.

<em>This is an automated message sent to moderators of the platform. Please do not reply.</em>
</x-mail::message>
