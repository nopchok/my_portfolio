import sys
sys.path.append('../scraper')
import db

import faiss
from llama_index.embeddings.huggingface import HuggingFaceEmbedding
from llama_index.core import StorageContext, VectorStoreIndex
from llama_index.core.node_parser import SimpleNodeParser
from llama_index.vector_stores.faiss import FaissVectorStore
from llama_index.core import Document


from langchain_community.llms import Ollama
import streamlit as st


# --- CACHE LOADING LOGIC ---
@st.cache_resource
def load_index():
    print("Initializing models and building index...")

    embedding_model = HuggingFaceEmbedding(model_name="sentence-transformers/paraphrase-multilingual-MiniLM-L12-v2")

    dimension = 384
    faiss_index = faiss.IndexFlatL2(dimension)
    vector_store = FaissVectorStore(faiss_index=faiss_index)
    storage_context = StorageContext.from_defaults(vector_store=vector_store)

    node_parser = SimpleNodeParser.from_defaults(chunk_size=128, chunk_overlap=20)
    
    all_forum = db.select_forum({})
    all_forum = [j for j in all_forum if j[3] != '']

    vector_index = VectorStoreIndex([], storage_context=storage_context, embed_model=embedding_model)

    for f in all_forum:
        metadata = {'id': f[1]}
        doc = Document(text=f[2], metadata=metadata)
        nodes = node_parser.get_nodes_from_documents([doc])
        for node in nodes:
            emb = embedding_model.get_text_embedding(node.text)
            node.embedding = emb
        vector_index.insert_nodes(nodes)

    retriever = vector_index.as_retriever(similarity_top_k=2)

    return embedding_model, retriever

@st.cache_resource
def load_llm():
    return Ollama(model="llama3.2:1b")


# --- RETRIEVAL LOGIC ---
def get_context_from_db(q, retriever):
    response = retriever.retrieve(q)
    print('retrieve doctor comment', len(response))

    retrieve_doctor_comment = ''
    for i, node in enumerate(response):
        forum = db.select_forum({'id': node.node.metadata['id']})
        if len(forum) == 1:
            retrieve_doctor_comment += "\n" + forum[0][3] + "\n"
    return retrieve_doctor_comment

def build_prompt(user_question, retriever):
    context_text = get_context_from_db(user_question, retriever)
    prompt = f"""You are a doctor.

Use the following conversation context to answer the patient's question:

Context:
{context_text}

Question:
{user_question}

Answer:"""
    return prompt


# --- UI ---
st.title("Tasks 1: LLM chatbot")

if "history" not in st.session_state:
    st.session_state.history = []

for msg in st.session_state.history:
    with st.chat_message(msg["role"]):
        st.write(msg["content"])

if user_input := st.chat_input("Say something"):
    with st.chat_message("user"):
        st.write(user_input)
    st.session_state.history.append({"role": "user", "content": user_input})

    # Get cached resources
    embedding_model, retriever = load_index()
    llm = load_llm()

    with st.chat_message("assistant"):
        response_stream = llm.stream(build_prompt(user_input, retriever))
        response = st.write_stream(response_stream)

    st.session_state.history.append({"role": "assistant", "content": response})


# streamlit run main.py
