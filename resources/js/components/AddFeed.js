import React, {Component} from "react";
import {Link} from 'react-router-dom';

class AddFeed extends Component {

    state = {
        title: '',
        link: '',
        source: '',
        source_url: '',
        publish_date: '',
        description: ''
    }

    handleInput = (e) => {
        this.setState({
            [e.target.name]: e.target.value
        });
    }

    saveFeed = async (e) => {
        e.preventDefault();

        const res = await axios.post('/api/add-feeds', this.state);

        if (res.data.status === 200) {

            console.log(res.data.message);
            this.setState({
                title: '',
                link: '',
                source: '',
                source_url: '',
                publish_date: '',
                description: ''
            });
        }
    }

    render() {
        return(
            <div className="container align-self-center">
                <div className="row">
                    <div className="col-md-6">
                        <div className="card">
                            <div className="card-header">
                                <h4>Add Feed
                                    <Link to={'/'} className="btn btn-primary btn-sm float-right"> Back</Link>
                                </h4>
                            </div>
                            <div className="card-body">
                                <form onSubmit={this.saveFeed}>
                                    <div className="form-group mb-3">
                                        <label>Title</label>
                                        <input type="text" name="title" value={this.state.title} onChange={this.handleInput} className="form-control"/>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Link</label>
                                        <input type="text" name="link" onChange={this.handleInput} value={this.state.link} className="form-control"/>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Source</label>
                                        <input type="text" name="source" onChange={this.handleInput} value={this.state.source} className="form-control"/>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Source URL</label>
                                        <input type="text" name="source_url" onChange={this.handleInput} value={this.state.source_url} className="form-control"/>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Publish Date</label>
                                        <input type="datetime-local" name="publish_date" onChange={this.handleInput} value={this.state.publish_date} className="form-control"/>
                                    </div>
                                    <div className="form-group mb-3">
                                        <label>Description</label>
                                        <textarea name="description" onChange={this.handleInput} value={this.state.description} className="form-control"/>
                                    </div>
                                    <div className="form-group mb-3">
                                        <button type="submit" className="btn btn-primary">Save Feed</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

export default AddFeed;


