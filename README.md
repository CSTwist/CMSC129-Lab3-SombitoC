# The Journal (AI-Powered)

The Journal is a digitalized personal journal application enhanced with AI integration. Built with Laravel and the MVC pattern, it allows users to manage their journal entries through a traditional UI and an intelligent AI Assistant.

## 🤖 Lab 3: AI Integration Features

### 1. AI Assistant (Expanded Requirements)
The application features a floating AI Assistant widget that can handle:
*   **Natural Language Inquiries**: Ask about your journals (e.g., "What did I write about lunch yesterday?").
*   **Full CRUD Operations**: 
    *   **Create**: "Add a new entry about my coding session."
    *   **Read**: "Show me my latest journals."
    *   **Update**: "Mark my entry about the exam as a favorite."
    *   **Delete**: "Remove the draft entry." (Requires confirmation).
*   **Trash Management**: "What's in my trash?" or "Restore the entry about the beach."
*   **Context Awareness**: The AI maintains a conversation history (last 10 messages), allowing for follow-up questions.
*   **Safety & UX**: Destructive operations (Delete/Update) trigger a confirmation prompt from the AI.

### 2. AI Model Used
*   **Primary**: Google Gemini 1.5 Flash (via Google AI Studio)
*   **Fallback**: Gemini 1.0 Pro
*   **Logic**: Implemented a robust fallback mechanism that automatically switches models if the primary one fails or hits rate limits.

---

## ⚙️ Installation and Setup
**Step 1. Clone the repository**
```bash
git clone https://github.com/Chak/CMSC129-Lab3-SombitoC
cd CMSC129-Lab3-SombitoC
```

**Step 2. Install Dependencies**
```bash
composer install
npm install
npm run build
```

**Step 3. Setup Environment Variables**
*   Duplicate `.env.example` to `.env`.
*   Generate key: `php artisan key:generate`.
*   **AI Setup**: Get a Gemini API Key from [Google AI Studio](https://aistudio.google.com/) and add it to your `.env`:
    ```env
    GEMINI_API_KEY=your_actual_api_key_here
    ```

**Step 4. Database Setup**
```bash
php artisan migrate:fresh --seed
```
*Note: The seeder populates the database with 20 dummy records (15 active, 5 trashed) to test the AI capabilities.*

---

## 💡 Example Queries to Try
*   "Show me my latest 5 journals."
*   "Did I mention anything about 'pizza' in my entries?"
*   "Summarize my journals from this week."
*   "Create a new entry titled 'Lab 3 Progress' with content 'I finished the AI integration today!'"
*   "Delete my entry about 'Draft notes'."
*   "Restore the journal I just deleted."

---

## 📸 AI Assistant in Action

![AI Chatbot](/public/images/screenshots/dashboard.png)
*AI Assistant integrated as a floating widget on the Dashboard.*

---

## 🚀 Technical Implementation Details
*   **Backend Proxy**: All AI calls are made through `AiChatController` to protect the API key.
*   **Function Calling**: Uses Gemini's `functionDeclarations` to map natural language to internal Eloquent operations.
*   **Session-based History**: Maintains context using Laravel's session store.
*   **Markdown Support**: The chat interface uses `marked.js` to render formatted text from the AI.
*   **Performance**: Implemented database indexing on `user_id` and `created_at` for faster AI data retrieval.
