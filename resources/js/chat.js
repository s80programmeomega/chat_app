let currentChannel = null;
let userChannel = null;

// Initialize global user channel and conversation handling
document.addEventListener("livewire:init", () => {
    // Get current user ID (you'll need to pass this from your Blade template)
    const userId = window.Laravel.user.id;

    // Subscribe to global user channel for conversation updates
    userChannel = window.Echo.private(`user.${userId}`)
        .listen('ConversationUpdated', (e) => {
            // Update sidebar for conversations not currently active
            if (currentChannel !== e.conversation_id) {
                Livewire.dispatch('conversationUpdated', e.conversation_id);
            }
        });

    // Handle conversation selection
    Livewire.on("joinConversation", (conversationId) => {
        // Leave previous conversation channel
        if (currentChannel) {
            window.Echo.leave(`conversation.${currentChannel}`);
        }

        // Join new conversation channel for real-time messages
        currentChannel = conversationId[0];
        window.Echo.join(`conversation.${currentChannel}`)
            .listen("MessageSent", (e) => {
                // Update active conversation messages
                Livewire.dispatch("newMessageReceived", e.message.conversation_id);

                // Auto-scroll to bottom
                const container = document.getElementById("messages-container");
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
    });
});


// let currentChannel = null;

// // Listen for conversation selection
// document.addEventListener("livewire:init", () => {
//     Livewire.on("joinConversation", (conversationId) => {
//         // Leave previous channel
//         if (currentChannel) {
//             window.Echo.leave(`conversation.${currentChannel}`);
//         }

//         // Join new conversation channel
//         currentChannel = conversationId[0];
//         window.Echo.join(`conversation.${currentChannel}`).listen(
//             "MessageSent",
//             (e) => {
//                 // Notify Livewire component about new message
//                 Livewire.dispatch("newMessageReceived", e.message.conversation_id);

//                 // Auto-scroll to bottom
//                 const container = document.getElementById("messages-container");
//                 if (container) {
//                     container.scrollTop = container.scrollHeight;
//                 }
//             }
//         );
//     });
// });
